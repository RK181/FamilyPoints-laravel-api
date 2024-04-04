<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    //

    public function createGroup(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                //'user_id' => 'required|integer', // creator_id
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
                    'status' => false,
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
                'status' => true,
                'message' => 'Success, Created Group'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
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
                    'status' => false,
                    'message' => 'No group found'
                ], 404);
            }

            $group->creator;
            $group->couple;
            return response()->json($group, 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
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
                'couple.email' => 'email|exists:users,email',
                'points_name' => 'string',
                'points_icon' => 'string',
                'conf_t_approve' => 'boolean',
                'conf_t_validate' => 'boolean',
                'conf_t_invalidate' => 'boolean',
                'conf_r_valiadte' => 'boolean'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            $user = $request->user();
            $group = $user->group;
            
            if ($group->couple_id == null && $request->couple['email'] != null) {
                $couple_id = User::select('id')->where('email', $request->couple['email'])->first()->id;  
                if ($user->id != $couple_id) {
                    $group->couple_id = $couple_id;
                }  
            }
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
                'status' => true,
                'message' => 'Success, Updated Group'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function deleteGroup(Request $request)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            // If not, error
            if ($group->id == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No group found'
                ], 404);
            }

            $group->delete();
            return response()->json([
                'status' => true,
                'message' => 'Success, Deleted Group'
            ], 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
}
