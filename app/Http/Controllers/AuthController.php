<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Register User
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Log user registration
        Log::info('User registered successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json(['user' => $user, 'message' => 'User registered successfully, A verification email has been sent to your email address.'], 201);
    }

    // Login User
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            // Log failed login attempt
            Log::warning('Failed login attempt', [
                'email' => $request->email,
            ]);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = $request->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log successful login
        Log::info('Login Successful', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);
    }

    // Logout User
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        // Log user logout
        Log::info('User logged out successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Update User Details
    public function update(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
        ]);

        // Update user details if provided in the request
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Log user update
        Log::info('User updated successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json(['user' => $user, 'message' => 'User details updated successfully']);
    }

    // Delete User
    public function delete(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        // Log user deletion
        Log::info('User deleted successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Delete user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
