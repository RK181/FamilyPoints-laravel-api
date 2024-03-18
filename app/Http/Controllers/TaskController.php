<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Create a new task.
     *
     * This method creates a new task with the provided parameters. The request must include the following parameters:
     * - title: The title of the task (required, string).
     * - description: The description of the task (required, string).
     * - reward: The reward for completing the task (required, integer).
     * - expire_at: The expiration date of the task in the format "d/m/Y" (required, date).
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status and message.
     */
    public function createTask(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), 
            [
                'title' => 'required|string',
                'description' => 'required|string',
                'reward' => 'required|integer',
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

            $task = new Task();
            $task->creator_id = $user->id; 
            $task->group_id = $group->id;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->reward = $request->reward;
            $task->expire_at = Carbon::createFromFormat('d/m/Y', $request->expire_at)->format('Y-m-d');
            $task->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Created Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Update a task.
     *
     * This method updates a task with the provided ID. The request must include the following parameters:
     * - title: The title of the task (required, string).
     * - description: The description of the task (required, string).
     * - reward: The reward for completing the task (required, integer).
     * - expire_at: The expiration date of the task in the format "d/m/Y" (required, date).
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param string $id The ID of the task to update.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status and message.
     */
    public function updateTask(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'id' => 'required|integer|exists:tasks,id',
                'title' => 'required|string',
                'description' => 'required|string',
                'reward' => 'required|integer',
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
            $task = Task::where('id', $request->id)->where('group_id', $group->id)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            $task->title = $request->title;
            $task->description = $request->description;
            $task->reward = $request->reward;
            $task->expire_at = Carbon::createFromFormat('d/m/Y', $request->expire_at)->format('Y-m-d');
            $task->save();

            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Update task approval status, approve task creation by couple or user(if configured).
     *
     * This method updates the approval status of a task with the provided ID. The request must include the following parameters:
     * - id (param): The task ID.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param string $id The ID of the task to update.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status and message.
     */
    public function updateTaskCreationApprove(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->where('user_id', null)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            if($user->id == $task->creator_id && $group->conf_t_approve){
                return response()->json([
                    'status' => false,
                    'message' => 'User not allowed'
                ], 403);
            }

            //if ($task->approve == false && (($user->id != $task->creator_id && $group->conf_t_approve) || $group->conf_t_approve == false)){
            if ($task->approve == false){
                $task->approve = true;
                $task->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Update task completion status, by User who complited the task. Need to wait for validation(if configured) to receive the reward.
     *
     * This method updates the completion status of a task with the provided ID. The request must include the following parameters:
     * - id (param): The task ID.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param string $id The ID of the task to update.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status and message.
     */
    public function updateTaskComplete(Request $request, string $id)
    {
        try {
            /*$request['id'] = $id;
            $validateUser = Validator::make($request->all(), 
            [
                'id' => 'required|integer|exists:tasks,id'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }*/

            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->where('user_id', null)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            if($task->approve == false && $group->conf_t_approve){
                return response()->json([
                    'status' => false,
                    'message' => 'User not allowed'
                ], 403);
            }

            if ($task->complete == false){
                DB::transaction(function () use ($task, $user, $group) {
                    // If dont need to wait for validation
                    if ($group->conf_t_validate == false) {
                        $task->validate = true;

                        $user->points = $user->points + $task->reward;
                        $user->save();
                    }
                    // If dont need to wait for approval
                    if ($group->conf_t_approve == false) {
                        $task->approve = true;
                    }

                    $task->user_id = $user->id;
                    $task->complete = true;
                    $task->save();
                });
            }

            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Update task validation status, validate task completion by couple or user(if configured).
     *
     * This method updates the validation status of a task with the provided ID. The request must include the following parameters:
     * - id (param): The task ID.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param string $id The ID of the task to update.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the status and message.
     */
    public function updateTaskCompletionValidation(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            if($task->complete == false || ($user->id == $task->user_id && $group->conf_t_validate)){
                return response()->json([
                    'status' => false,
                    'message' => 'User not allowed'
                ], 403);
            }

            if ($task->validate == false){
                DB::transaction(function () use ($task) {
                    $task_user = $task->user;
                    $task_user->points = $task_user->points + $task->reward;
                    $task_user->save();
    
                    $task->validate = true;
                    $task->save();
                });
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function updateTaskCompletionInValidation(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            if($group->conf_t_invalidate == false || $task->validate == false || $user->id == $task->user_id){
                return response()->json([
                    'status' => false,
                    'message' => 'User not allowed'
                ], 403);
            }

            if ($task->validate){
                DB::transaction(function () use ($task) {
                    $task->validate = false;
                    $task->complete = false;

                    $task_user = $task->user;
                    $task_user->points = $task_user->points - $task->reward;
                    $task_user->save();

                    $task->user_id = null;
                    $task->save();
                }); 
            }

            return response()->json([
                'status' => true,
                'message' => 'Success, Updated Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getTaskById(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            $task->creator;
            $task->user;

            return response()->json($task, 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    public function getGroupTaskList(Request $request)
    {
        try {
            $user = $request->user();
            $group = $user->group;

            // Get all tasks from group
            $tasks = $group->tasks()->with('creator')->with('user')->get();

            return response()->json($tasks, 200);
            
        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    // Delete a task
    public function deleteTask(Request $request, string $id)
    {
        try {
            $user = $request->user();
            $group = $user->group;
            $task = Task::where('id', $id)->where('group_id', $group->id)->first();
            if ($task == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Task not found'
                ], 404);
            }

            $task->delete();

            return response()->json([
                'status' => true,
                'message' => 'Success, Deleted Task'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
}
