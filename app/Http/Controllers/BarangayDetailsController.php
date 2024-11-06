<?php

namespace App\Http\Controllers;

use App\Models\BarangayDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\BarangayDetailsRequest;
use App\Http\Resources\BarangayDetailsResource;
use Illuminate\Support\Facades\Storage;

class BarangayDetailsController extends Controller
{
    public function index()
    {
        $details = BarangayDetails::first();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', new BarangayDetailsResource($details));
    }

    public function update(BarangayDetailsRequest $request, $id)
    {
        $details = $this->findDataOrFail(BarangayDetails::class, $id);

        if ($details instanceof \Illuminate\Http\JsonResponse) {
            return $details;
        }

        $imageName = $this->handleImageUpload($request->image, $details->image, 'barangay_details', 'image_');
        $logoName = $this->handleImageUpload($request->logo, $details->logo, 'barangay_details', 'logo_');

        $details->update([
            'name' => $request->name,
            'image' => $imageName ? '/storage/barangay_details/' . $imageName : ($request->image === null ? null : $details->image),
            'logo' => $logoName ? '/storage/barangay_details/' . $logoName : ($request->logo === null ? null : $details->logo),
        ]);

        return $this->jsonResponse(true, 200, 'Barangay Details updated successfully', $details);
    }

    /**
     * Handle image upload and deletion of old image if needed.
     *
     * @param string|null $base64Image
     * @param string|null $currentImage
     * @param string $folder
     * @param string $prefix
     * @return string|null
     */
    private function handleImageUpload($base64Image, $currentImage, $folder, $prefix)
    {
        if ($base64Image === null) {
            if ($currentImage) {
                $this->deleteOldImage($currentImage, $folder);
            }
            return null;
        }

        if ($base64Image === $currentImage) {
            return null;
        }

        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $imageType = strtolower($type[1]);

            if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
            }

            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $imageName = $prefix . time() . '_' . uniqid() . '.' . $imageType;

            Storage::disk('public')->put($folder . '/' . $imageName, $imageData);

            // Delete the old image if it exists
            if ($currentImage) {
                $this->deleteOldImage($currentImage, $folder);
            }

            return $imageName;
        }

        return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
    }

    /**
     * Delete the old image from storage.
     *
     * @param string $currentImage
     * @param string $folder
     * @return void
     */
    private function deleteOldImage($currentImage, $folder)
    {
        $oldImageName = basename($currentImage);
        if (Storage::disk('public')->exists($folder . '/' . $oldImageName)) {
            Storage::disk('public')->delete($folder . '/' . $oldImageName);
        }
    }
}
