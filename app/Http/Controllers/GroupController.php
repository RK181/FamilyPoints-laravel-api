<?php

namespace App\Http\Controllers;

use App\Models\Group;
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
            $group->points_icon = $request->points_icon ? $request->points_icon : false;
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

    public function readGroup(Request $request)
    {
        return '';
    }

    public function updateGroup(Request $request)
    {
        return '';
    }

    public function deleteGroup(Request $request)
    {
        return '';
    }
}
