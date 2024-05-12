<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use App\Notifications\InviteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    //

    public function createGroup(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'points_name' => 'required|string',
                'points_icon' => 'required|string',
                'conf_t_approve' => 'boolean',
                'conf_t_validate' => 'boolean',
                'conf_t_invalidate' => 'boolean',
                'conf_r_valiadte' => 'boolean'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            $user = $request->user();

            $group = new Group();
            $group->creator_id = $user->id;
            $group->name = $request->name;
            $group->points_name = $request->points_name;
            $group->points_icon = $request->points_icon;
            $group->conf_t_approve = $request->conf_t_approve ? $request->conf_t_approve : false;
            $group->conf_t_validate = $request->conf_t_validate ? $request->conf_t_validate : false;
            $group->conf_t_invalidate = $request->conf_t_invalidate ? $request->conf_t_invalidate : false;
            $group->conf_r_valiadte = $request->conf_r_valiadte ? $request->conf_r_valiadte : false;
            $group->save();
            
            return response()->json([
                'message' => 'Success, Created Group'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getGroup(Request $request)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            // If not, error
            if ($group->id == 0) {
                return response()->json([
                     
                    'message' => 'No group found'
                ], 404);
            }

            $group->creator;
            $group->couple;
            return response()->json($group, 200);
            
        } catch (\Throwable) {
            return response()->json([
                 
                'message' => 'Server error'
            ], 500);
        }
    }

    // Protected Authorization required
    public function updateGroup(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'points_name' => 'string',
                'points_icon' => 'string',
                'conf_t_approve' => 'boolean',
                'conf_t_validate' => 'boolean',
                'conf_t_invalidate' => 'boolean',
                'conf_r_valiadte' => 'boolean'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            $user = $request->user();
            $group = $user->group;
            
            if ($request->has('name')) {
                $group->name = $request->name;
            }
            if ($request->has('points_name')) {
                $group->points_name = $request->points_name;                      
            }
            if ($request->has('points_icon')){
                $group->points_icon = $request->points_icon;                     
            }
            if ($request->has('conf_t_approve')) {
                $group->conf_t_approve = $request->conf_t_approve;                     
            }
            if ($request->has('conf_t_validate')) {
                $group->conf_t_validate = $request->conf_t_validate;                   
            }
            if ($request->has('conf_t_invalidate')) {
                $group->conf_t_invalidate = $request->conf_t_invalidate;          
            }
            if ($request->has('conf_r_valiadte')){
                $group->conf_r_valiadte = $request->conf_r_valiadte;
            }
            
            $group->save();
            
            return response()->json([
                'message' => 'Success, Updated Group'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }

    public function deleteGroup(Request $request)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $group->delete();

            return response()->json([
                'message' => 'Success, Deleted Group'
            ], 200);
            
        } catch (\Throwable) {
            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Send Invitation
     * @param string $id
     */
    public function sendInvitation(Request $request, string $email) {
        try {
            $validateUser = Validator::make(['email' => $email], [
                'email' => 'required|email'
            ]);
            
            $couple = User::where('email', $email)->first();
            if ($couple == null) {
                $validateUser->errors()->add(
                    'email', 'User not found'
                );
                return response()->json([
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }
            $user = $request->user();
            if ($user->id == $couple->id || $user->group->couple_id != 0) {
                $validateUser->errors()->add(
                    'email', 'Couple can not be already in a group or already have a couple'
                );
                return response()->json([
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }
            if (!$couple->hasVerifiedEmail()) {
                $validateUser->errors()->add(
                    'email', 'Couple need to verify email'
                );
                return response()->json([
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }
            
            $token = random_bytes(32);
            $token = $user->email . $token;
            $hashedToken = hash('sha256', $token);

            $url = URL::temporarySignedRoute(
                'invitation.accept', now()->addMinutes(300), ['id' => $couple->id, 'token' => $hashedToken]
            );
            Notification::route('mail', $email)->notify(new InviteNotification($user->name ,$url));
            $user->invitation_token = $hashedToken;
            $user->save();

            return response()->json([
                'message' => 'Invitation sent to the user email'
            ], 200);
        } catch (\Throwable) {
            return response()->json([
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Accept Invitation
     * @param string $id
     */
    public function acceptInvitation(string $id, string $token) {
        try {
            // obtenemos el usuario
            $couple = User::where('id', $id)->first();
            $user = User::where('invitation_token', $token)->first();
            // si el usuario no existe o el token no es el mismo
            if ($couple->group->id == 0 && $user->group->id != 0) {
                $group = $user->group;
                if ($group->couple_id == 0) {
                    DB::transaction(function () use ($user, $group, $couple) {
                        $group->couple_id = $couple->id;
                        $group->save();
                        $user->invitation_token = null;
                        $user->save();
                    });
                }
            }
            return view('status')->with([
                'header' => 'Invitation',
                'message' => 'Invitation accepted successfully'
            ]); 

        } catch (\Throwable) {
            return abort(500, 'Server error: algo ha ido mal intentalo mas tarde');
        }
    }
}
