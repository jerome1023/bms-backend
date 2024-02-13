<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
                'status_code' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => $user->createToken('Web API')->plainTextToken,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'role' => $user->role_id,
            'status_code' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role = Role::where('name', $request->role)->first();

        $user = User::create([
            'id' => Uuid::uuid4(),
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'role_id' => $role->id,
            'archive_status' => false
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
            'status_code' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }
}
