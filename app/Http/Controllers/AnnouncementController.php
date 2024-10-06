<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcement = Announcement::where('archive_status', false)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', AnnouncementResource::collection($announcement));
    }

    public function archive_list()
    {
        $announcement = Announcement::where('archive_status', true)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', AnnouncementResource::collection($announcement));
    }

    public function store(AnnouncementRequest $request)
    {
        $userId = auth()->id();

        $base64Image = $request->image;
        $imageName = null;

        if ($base64Image) {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $imageType = strtolower($type[1]);

                if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
                    return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
                }

                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $imageName = time() . '.' . $imageType;
                Storage::disk('public')->put('images/' . $imageName, $image);
            } else {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
            }
        }

        Announcement::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'what' => $request->what,
            'where' => $request->where,
            'who' => $request->who,
            'when' => $request->when,
            'details' => $request->details,
            'image' => $imageName ? '/storage/images/' . $imageName : null,
            'archive_status' => false
        ]);
        return $this->jsonResponse(true, 201, 'Announcement created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);
        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }
        return $this->jsonResponse(true, 201, 'Data retrieved successfully', new AnnouncementResource($announcement));
        //
    }

    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);

        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }

        $base64Image = $request->image;
        $imageName = null;

        if ($base64Image === null) {
            // Delete the old image from storage if it exists
            if ($announcement->image) {
                $oldImageName = basename($announcement->image);
                if (Storage::disk('public')->exists('images/' . $oldImageName)) {
                    Storage::disk('public')->delete('images/' . $oldImageName);
                }
            }
        } elseif ($base64Image && $base64Image != $announcement->image) {
            // Handle image upload/change
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $imageType = strtolower($type[1]);

                if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
                    return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
                }

                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
                $imageName = time() . '.' . $imageType;
                Storage::disk('public')->put('images/' . $imageName, $image);
            } else {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
            }

            // Delete the old image from storage if a new one is uploaded
            if ($announcement->image) {
                $oldImageName = basename($announcement->image);
                if (Storage::disk('public')->exists('images/' . $oldImageName)) {
                    Storage::disk('public')->delete('images/' . $oldImageName);
                }
            }
        }

        $announcement->update([
            'what' => $request->what,
            'where' => $request->where,
            'who' => $request->who,
            'when' => $request->when,
            'details' => $request->details,
            'image' => $imageName ? '/storage/images/' . $imageName : ($base64Image === null ? null : $announcement->image),
            'archive_status' => $request->archive_status ?? false
        ]);
        return $this->jsonResponse(true, 200, 'Announcement updated successfully', $announcement);
    }

    public function archive($id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);
        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }

        $announcement->archive_status = true;
        $announcement->save();

        return $this->jsonResponse(true, 200, 'Announcement archived successfully');
    }

    public function destroy($id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);

        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }

        $announcement->delete();
        return $this->jsonResponse(true, 200, 'Announcement deleted successfully');
    }
}
