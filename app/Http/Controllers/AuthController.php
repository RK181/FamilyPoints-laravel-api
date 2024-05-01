<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Sign Up
     * @param Request $request
     */
    public function signUp(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            event(new Registered($user));

            return response()->json([
                'status' => true,
                'message' => 'Success, SignUp'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * LogIn
     * @param Request $request
     * @return User token
     */
    public function logIn(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), 
            [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'BadRequest',
                    'errors' => $validateUser->errors()
                ], 400);
            }
            
            $user = User::where('email', $request->email)->first();

            if(! $user || ! Hash::check($request->password, $user->password)){
                return response()->json([
                    'status' => false,
                    'message' => 'The provided credentials are incorrect.',
                ], 401);
            }

            return response()->json([
                'status' => true,
                'message' => 'Success, LogIn',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * LogOut
     * @param Request $request
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();

            return response()->json([
                'status' => true,
                'message' => 'Success, LogOut'
            ], 200);

        } catch (\Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Server error'
            ], 500);
        }
    }
    public function verifyEmail(string $id) {
        // obtenemos el usuario
        $user = User::findOrFail($id);
        // verificamos si el usuario ya ha verificado su email
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    
        return redirect()->to('/');
    }
    
    /**
     * Resend Verify Email
     * @param EmailVerificationRequest $request
     */
    public function resendVerifyEmail(Request $request) {
        $user = $request->user();
    
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Email verification link sent on your email'
        ], 200);
    }

}

