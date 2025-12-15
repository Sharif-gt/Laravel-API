<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request) {
        // validate data
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required| email|unique:users,email',
                'password' => 'required'
            ]);
        // if validation fails
        if($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors'=> $validateUser->errors()->all()
            ],401);
        }
        // if validation success
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully.'
        ]);
    }

    public function login(Request $request) {
        $requestUser = Validator::make(
            $request->all(),
            [
            'email' => 'required|email',
            'password' => 'required'
            ]);

        if($requestUser->fails()) {
            return response()->json([
                'status' => false,
                'error' => $requestUser->errors()->all()
            ]);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'Logged in successful',
                'token' => $auth->createToken('API Token')->plainTextToken,
                'token_typr' => 'bearer'

            ]);
        } else{
            return response()->json([
                'status' => false,
                'message' => 'Email and Password not match'
            ]);
        }
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message'=> 'Logged out successful'
        ]);
    }
}
