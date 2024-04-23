<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
        try {
        //  Validate Request
            $validated_data = 
            request()->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

        //  Find User by Email
            if (!Auth::attempt($validated_data)) {
                return ResponseFormatter::error('Unauthorized', 401);
            }

            $user = User::where('email', $validated_data['email'])->first();
            if (!Hash::check($validated_data['password'], $user->password)) {
                throw new Exception('Invalid Password');
            }
        //  Generate Token
            $token_result = $user->createToken('auth_token')->plainTextToken;
            
        //  Return Response
            return ResponseFormatter::success([
                'access_token' => $token_result,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login success');
        } catch (Exception $e) {
            return ResponseFormatter::error('Authentication Failed');
        }
    }
}
