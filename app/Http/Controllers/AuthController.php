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
                'status' => Response::HTTP_UNAUTHORIZED
            ], Response::HTTP_UNAUTHORIZED);
        }

        // dd($user->id);
        return response()->json([
            'token' => $user->createToken('Web API')->plainTextToken,
            'id' => $user->id,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'role' => $user->role_id,
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }

    public function register(Request $request)
    {
        $validateRules = [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|string|exists:roles,name'
        ];
        
        if ($request->role == 'User') {
            $validateRules['address'] = 'required|string';
            $validateRules['gender'] = 'required|string';
        }
        
        $validateUser = Validator::make($request->all(), $validateRules);

        if ($validateUser->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
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
            'message' => 'User created successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
            'status' => Response::HTTP_CREATED
        ], Response::HTTP_CREATED);
    }
}
