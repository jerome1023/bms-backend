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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'archive_status' => $request->archive_status ?? false,
        ]);
        return $this->jsonResponse(true, 200, 'Profile updated successfully');
    }

    public function update_profile(Request $request)
    {
        /** @var User $user */
        $user = auth()->user(); // Get the authenticated user

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validateRules = [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|same:confirm_password',
            'confirm_password' => 'nullable|min:6',
        ];

        if ($user->role->name == 'User') {
            $validateRules['address'] = 'required|string';
            $validateRules['gender'] = 'required|string';
        }

        $validateUser = Validator::make($request->all(), $validateRules);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validateUser->errors(),
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $base64Image = $request->image;
        $imageName = null;

        if ($base64Image === null) {
            // Delete the old image from storage if it exists
            if ($user->image) {
                $oldImageName = basename($user->image);
                if (Storage::disk('public')->exists('profile/' . $oldImageName)) {
                    Storage::disk('public')->delete('profile/' . $oldImageName);
                }
            }
        } elseif ($base64Image && $base64Image != $user->image) {
            // Handle image upload/change
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $imageType = strtolower($type[1]);

                if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
                    return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
                }

                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $imageName = time() . '.' . $imageType;
                Storage::disk('public')->put('profile/' . $imageName, $image);
            } else {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
            }

            // Delete the old image from storage if a new one is uploaded
            if ($user->image) {
                $oldImageName = basename($user->image);
                if (Storage::disk('public')->exists('profile/' . $oldImageName)) {
                    Storage::disk('public')->delete('profile/' . $oldImageName);
                }
            }
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            // 'gender' => $request->gender,
            'email' => $request->email,
            'address' => $request->address,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'image' =>  $imageName ? '/storage/profile/' . $imageName : ($base64Image === null ? null : $user->image),
            'archive_status' => $request->archive_status ?? false,
        ]);
        return $this->jsonResponse(true, 200, 'Profile updated successfully', new UserResource($user));
    }
}
