<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password as RulesPassword;

use function Laravel\Prompts\error;

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

    public function register(Request $request)
    {
        try {
            // Validate Request
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'string', 'max:255', 'unique:users'], 
                'password' => ['required', 'string', new RulesPassword],
            ]);
            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Generate Token
            $token_result = $user->createToken('auth_token')->plainTextToken;

            // Return Response
            return ResponseFormatter::success([
                'access_token' => $token_result,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Register success');
        } catch (Exception $error) {
            // Return Error Response
            return ResponseFormatter::error($error->getMessage());
        }
    }

    public function logout(Request $request)
    {
        // Revoke Token
        $token = $request->user()->currentAccessToken()->delete();

        // Return Response
        return ResponseFormatter::success($token, 'Logout Success');
    }

    public function fetch(Request $request)
    {
        // Get User
        $user = $request->user();

        // Return Response
        return ResponseFormatter::success($user, 'Fetch Success');
    }
}
