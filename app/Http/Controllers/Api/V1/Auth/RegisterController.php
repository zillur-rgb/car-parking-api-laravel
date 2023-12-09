<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        // Create a new user based on validated data
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        // Trigger the Registered event for the new user
        event(new Registered($user));

        // Get a substring of the user agent (device) for token creation
        $device = substr($request->userAgent() ?? '', 0, 255);

        // Return a JSON response with the access token and HTTP status code 201 (Created)
        return response()->json([
            'access_token' => $user->createToken($device)->plainTextToken
        ], Response::HTTP_CREATED);
    }
}
