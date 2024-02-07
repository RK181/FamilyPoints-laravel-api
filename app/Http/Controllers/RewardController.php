<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
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
                'expire_at' => 'required|date|date_format:d/m/Y'
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

            if ($group == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Group not found'
                ], 404);
            }

            $reward = new Reward();
            $reward->user_id = $user->id;
            $reward->group_id = $group->id;
            $reward->title = $request->title;
            $reward->description = $request->description;
            $reward->cost = $request->cost;
            $reward->expire_at = $request->expire_at;
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

    public function getReward(Request $request, string $id)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            // If not, error
            if ($group == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'No group found'
                ], 404);
            }
            
            $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
            
            return response()->json([
                'status' => true,
                'group' => $reward
            ], 200);
            
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
            $reward = null;
            // TO-DO -> Mover la comprobacion a un Middleware
            if ($group->id == null) {
                $reward = Reward::where('id', $request->id)->where('group_id', $group->id)->first();
                if ($reward == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No group found'
                    ], 404);
                }
            }
            
            if ($request->title != null) {
                $reward->title = $request->title;
            }
            if ($request->description != null) {
                $reward->description = $request->description;                      
            }
            if ($request->cost != null) {
                $reward->cost = $request->cost;                     
            }
            if ($request->expire_at != null) {
                $reward->expire_at = $request->expire_at;                     
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

    public function deleteReward(Request $request, string $id)
    {
        try {
            // Get user 
            $user = $request->user();
            // Get group if exist
            $group = $user->group;
            $reward = null;
            // If not, error
            // TO-DO
            if ($group->id == null) {
                $reward = Reward::where('id', $id)->where('group_id', $group->id)->first();
                if ($reward == null) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No group found'
                    ], 404);
                }
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
