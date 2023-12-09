<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // Attempt to find a user with the provided email address
        $user = User::where('email', $request->email)->first();

        // Check if the user does not exist or if the provided password is incorrect
        if (!$user || !Hash::check($request->password, $user->password)) {
            // If validation fails, throw a ValidationException with a custom error message
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect!'],
            ]);
        }

        // Extract device information from the request and set token expiration based on 'remember' option
        $device = substr($request->userAgent() ?? '', 0, 255);
        $expiresAt = $request->remember ? null : now()->addMinutes(config('session.lifetime'));

        // If validation is successful, generate an access token and return it in the response
        return response()->json([
            'access_token' => $user->createToken($device, expiresAt: $expiresAt)->plainTextToken,
        ], Response::HTTP_CREATED);
    }
}
