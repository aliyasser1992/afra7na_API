<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                "message" => "Invalid user details"
            ], 401);
        }
        $user->delete();

        return response()->json([
            'message' => "User deleted"
        ]);
    }
}
