<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //Regster API (name, email, password, confirm_password)
    public function register(Request $request){
        $data = $request-> validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed'
        ]);

        User::create($data);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully'
        ]);
    }

    //Login API (email, password)
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(!Auth::attempt($request->only("email", "password"))){
            return response()->json([
                'status' => false,
                'message' => 'Invalid Credentials'
            ]);
        }

        $user = Auth::user();

        $token = $user->createToken("myToken")->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => "User logged in successfully",
            "token" => $token
        ]);
    }

    //Profile API
    public function profile(){
        $user = Auth::user();

        return response()->json([
            "status" => true,
            "message" => "User profile data",
            "user" => $user
        ]);
    }

    //Logout API
    public function logout(){
        Auth::logout();

        return response()->json([
            "status"=> true,
            "message"=> "User logged out successfully"
        ]);
    }
}
