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
    //
    public function view(string $id): JsonResponse
    {
        $user = User::where(['id' => $id])->first();

        // return (new UserResource($user))->response()->setStatusCode(Response::HTTP_OK);
        return response()->json([
            'data' => new UserResource($user),
            'status' => Response::HTTP_OK
        ], Response::HTTP_OK);
    }
}
