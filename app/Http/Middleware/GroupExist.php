<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GroupExist
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $group = $user->group;
        //$request->user()->group->exists())
        if ($group->id == 0) {
            return response()->json([
                'status' => false,
                'message' => 'No group found'
            ], 404);
        }
        
        return $next($request);
    }
}
