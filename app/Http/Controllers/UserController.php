<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        $role = Role::where('name', 'User')
            ->with(['users' => function ($query) {
                $query->where('archive_status', false);
            }])
            ->first();

        $users = $role ? $role->users : collect();

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', UserResource::collection($users));
    }

    public function view(string $id): JsonResponse
    {
        $user = $this->findDataOrFail(User::class, $id);

        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        return $this->jsonResponse(true, Response::HTTP_OK, 'Data retrieved successfully', new UserResource($user));
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->findDataOrFail(User::class, $id);

        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'email' => $request->email,
            'address' => $request->address,
            // 'password' => $request->password ? Hash::make($request->password) : $user->password,
            // 'archive_status' => $request->archive_status ?? false,
        ]);
        return $this->jsonResponse(true, 200, 'Profile updated successfully');
    }
}
