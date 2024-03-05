<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Support\Str;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcement = Announcement::all();
        return $this->jsonResponse(200, 'Data retrieved successfully', AnnouncementResource::collection($announcement));
    }

    public function store(AnnouncementRequest $request)
    {
        $userId = auth()->id();

        Announcement::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'what' => $request->what,
            'where' => $request->where,
            'who' => $request->who,
            'when' => $request->when,
            'details' => $request->details,
            'image' => $request->image,
            'archive_status' => false
        ]);
        return $this->jsonResponse(201, 'Announcement created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $Announcement)
    {
        //
    }

    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);

        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }

        $announcement->update([
            'what' => $request->what,
            'where' => $request->where,
            'who' => $request->who,
            'when' => $request->when,
            'details' => $request->details,
            'image' => $request->image,
            'archive_status' => $request->archive_status ?? false
        ]);
        return $this->jsonResponse(200, 'Announcement updated successfully', $announcement);
    }

    public function destroy($id)
    {
        $announcement = $this->findDataOrFail(Announcement::class, $id);

        if ($announcement instanceof \Illuminate\Http\JsonResponse) {
            return $announcement;
        }

        $announcement->delete();
        return $this->jsonResponse(200, 'Announcement deleted successfully');
    }
}
