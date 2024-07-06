<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $request){
        $validUser = Validator::make(
           $request->all(),
           [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required',
           ]
        );
        if($validUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'validation failed',
                    'errors'=>$validUser->errors()->all(),
                ],401);
        }
        $user= User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'user created successfully',
            'user'=>$user,
        ],200);
    }

    public function login(Request $request){
        $validUser=Validator::make(
            $request->all(),
            [
               'email'=>'required|email',
               'password'=>'required',
            ]
        );
        if($validUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Authentication failed',
                'errors'=>$validUser->errors()->all(),
            ],404);
        }
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $authuser=Auth::user();
            return response()->json([
                'status'=>true,
                'message'=>'user logged in successfully',
                'token'=>$authuser->createToken("API TOKEN")->plainTextToken,
                'token_type'=>'bearer',
            ],200);
        }
        else{
            return response()->json([
                'status'=>false,
                'message'=>'Authentication failed',

            ],401);
        }

    }
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully',
        ], 200);
    }
}
