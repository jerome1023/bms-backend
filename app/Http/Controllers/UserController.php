<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function view(string $id): JsonResponse
    {
        $user = $this->findDataOrFail(User::class ,$id);

        if ($user instanceof \Illuminate\Http\JsonResponse) {
            return $user;
        }

        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Data retrieved successfully',
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }
}
