<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;


class UserController extends Controller
{

public function register(Request $request)
{

    if ($request->isMethod('post')) {
        // Get input data from the request
        $userData = $request->all();

        if (empty($userData['name']) || empty($userData['email']) || empty($userData['password'])) {
            $message = "Please enter complete user details!";
            return response()->json([
                "status" => false,
                "message" => $message
            ]);
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                "status" => false,
                "message" => "Invalid email format"
            ]);
        }

        // Check if the user already exists
        $existingUser = User::where('email', $userData['email'])->first();
        if ($existingUser) {
            return response()->json([
                "status" => false,
                "message" => "User with this email already exists"
            ]);
        }


        $user = new User();
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = bcrypt($userData['password']);
        $user->save();

        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return the user and token in the response
        return response()->json([
            "status" => true,
            "message" => 'User registered successfully!',
            "data" => [
                "user" => $user,
                "token" => $token
            ]
        ]);



    }
}
public function login(Request $request){
  // Check if the request is a POST request
  if ($request->isMethod('post')) {
    // Get input data from the request
    $userData = $request->all(); // Use all() to get all data from the request

    // Validation: Check if email and password are provided
    if (empty($userData['email']) || empty($userData['password'])) {
        $message = "Please enter both email and password!";
        return response()->json([
            "status" => false,
            "message" => $message
        ]);
    }

    // Validate email format
    if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            "status" => false,
            "message" => "Invalid email format"
        ]);
    }

    // Check if the user exists
    $user = User::where('email', $userData['email'])->first();

    if (!$user) {
        return response()->json([
            "status" => false,
            "message" => "User not found"
        ]);
    }

    // Attempt to login using the provided credentials
    if (Auth::attempt(['email' => $userData['email'], 'password' => $userData['password']])) {
        // If authentication is successful, get the authenticated user
        $authenticatedUser = Auth::user();

        // Generate a token for the authenticated user (using Sanctum or Passport)
        $token = $authenticatedUser->createToken('Easybill')->plainTextToken;

        return response()->json([
            "status" => true,
            "message" => 'Login successful!',
            "data" => [
                "user" => $authenticatedUser,
                "token" => $token
            ]
        ]);
    } else {
        return response()->json([
            "status" => false,
            "message" => "Invalid credentials"
        ]);
    }
}


}

public function update(Request $request){
// Check if the request is a PUT/PATCH request
if ($request->isMethod('put') || $request->isMethod('patch')) {
    // Get the authenticated user
    $user = Auth::user();

    // Validate the incoming request data
    $validatedData = $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users,email,' . $user->id, // Ensure email is unique except for the current user
        'password' => 'nullable|min:6|confirmed', // Password must be confirmed and at least 6 characters long
    ]);

    // Update user data if the fields are provided
    if ($request->has('name')) {
        $user->name = $validatedData['name'];
    }

    if ($request->has('email') && $validatedData['email'] !== $user->email) {
        $user->email = $validatedData['email'];
    }

    if ($request->has('password')) {
        // Hash the new password before saving
        $user->password = Hash::make($validatedData['password']);
    }

    // Save the updated user data to the database
    $user->save();

    // Return success response
    return response()->json([
        'status' => true,
        'message' => 'User updated successfully!',
        'data' => [
            'user' => $user
        ]
    ]);
}

// If the request is not PUT/PATCH
return response()->json([
    'status' => false,
    'message' => 'Invalid request method. Use PUT or PATCH.'
]);
}


public function delete(Request $request, $id)
{

  // Check if the request is using the DELETE method
  if ($request->isMethod('delete')) {

    // Get the currently authenticated user
    $authenticatedUser = $request->user();

    // Ensure the authenticated user is allowed to delete (e.g., only admins or the user themselves)
    if ($authenticatedUser->id !== $id) {
        return response()->json([
            "status" => false,
            "message" => "Unauthorized to delete this user."
        ], 403);
    }

    // Find the user by ID
    $user = User::find($id);

    // If user not found, return error
    if (!$user) {
        return response()->json([
            "status" => false,
            "message" => "User not found."
        ], 404);
    }

    // Delete the user
    $user->delete();

    // Return success response
    return response()->json([
        "status" => true,
        "message" => "User deleted successfully!"
    ]);
}

// If the request method is not DELETE
return response()->json([
    "status" => false,
    "message" => "Invalid request method. Use DELETE."
]);

}
}



