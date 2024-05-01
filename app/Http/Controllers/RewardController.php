<?php

namespace App\Http\Controllers;

use App\Models\Reward;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class RewardController extends Controller
{
    public function createReward(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'cost' => 'required|integer',
                'expire_at' => 'required|date_format:d/m/Y'
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

            $reward = new Reward();
            $reward->user_id = $user->id;
            $reward->group_id = $group->id;
            $reward->title = $request->title;
            $reward->description = $request->description;
            $reward->cost = $request->cost;
            $reward->expire_at = Carbon::createFromFormat('d/m/Y', $request->expire_at)->format('Y-m-d');
            $reward->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Created Reward'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getRewardById(Request $request, string $id)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            
            $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
            if ($reward == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No reward found'
                ], 404);
            }

            $reward->user;

            return response()->json($reward, 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getGroupRewardList(Request $request)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            
            $rewards = $group->rewards()->with('user')->get();
            //$rewards->user;

            return response()->json($rewards, 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    // Protected Authorization required
    public function updateReward(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'id' => 'required|integer|exists:rewards,id',
                'title' => 'string',
                'description' => 'string',
                'cost' => 'integer',
                'expire_at' => 'date|date_format:d/m/Y'
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

            $reward = Reward::where('id', $request->id)->where('group_id', $group->id)->first();
            if ($reward == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No reward found'
                ], 404);
            }
            
            if ($request->has('title')) {
                $reward->title = $request->title;
            }
            if ($request->has('description')) {
                $reward->description = $request->description;                      
            }
            if ($request->has('cost')) {
                $reward->cost = $request->cost;                     
            }
            if ($request->has('expire_at')) {
                $reward->expire_at = Carbon::parse($request->expire_at)->format('Y-m-d');                     
            }

            $reward->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Reward'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    // Pedir o canjear la recompensa
    public function updateRewardRedeem(Request $request, string $id)
    {
        try{
            $user = $request->user();
            $group = $user->group;

            $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
            if ($reward == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No reward found'
                ], 404);
            }

           if ($reward->user_id != $user->id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden'
                ], 403);
            }
            
            if ($reward->redeem == false) {
                // Si para canjear la recompensa no es necesario validar el canjeo a posteriori
                if ($group->conf_r_valiadte == false) {
                    $couple = $reward->user;
                    $couple->points = $couple->points - $reward->cost;
                    $couple->save();

                    $reward->validate = true;
                }
                $reward->redeem = true;
                $reward->save();
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Redeem Reward'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    // Validar el canjeo de la recompensa
    public function updateRewardValidate(Request $request, string $id)
    {
        try{
            $user = $request->user();
            $group = $user->group;

            $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
            if ($reward == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No reward found'
                ], 404);
            }

           if ($reward->user_id == $user->id && $group->conf_r_valiadte) {
                return response()->json([
                    'status' => false,
                    'message' => 'Forbidden'
                ], 403);
            }

            if ($reward->validate == false && $reward->redeem) {
                $couple = $reward->user;
                $couple->points = $couple->points - $reward->cost;
                $couple->save();

                $reward->validate = true;
                $reward->save();
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Validate Reward'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function deleteReward(Request $request, string $id)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            
            $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
            if ($reward == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No reward found'
                ], 404);
            }

            $reward->delete();

            return response()->json([
                'status' => true,
                'message' => 'Success, Deleted Reward'
            ], 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
}
