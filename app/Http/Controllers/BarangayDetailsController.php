<?php

namespace App\Http\Controllers;

use App\Models\BarangayDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\BarangayDetailsRequest;
use App\Http\Resources\BarangayDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangayDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $details = BarangayDetails::first();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', new BarangayDetailsResource($details));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BarangayDetailsRequest $request, $id)
    {
        $details = $this->findDataOrFail(BarangayDetails::class, $id);

        if ($details instanceof \Illuminate\Http\JsonResponse) {
            return $details;
        }

        // $base64Image = $request->image;
        // $base64Logo = $request->logo;
        // $imageName = null;
        // $logoName = null;

        // if ($base64Image === null) {
        //     // Delete the old image from storage if it exists
        //     if ($details->image) {
        //         $oldImageName = basename($details->image);
        //         if (Storage::disk('public')->exists('barangay_details/' . $oldImageName)) {
        //             Storage::disk('public')->delete('barangay_details/' . $oldImageName);
        //         }
        //     }
        // } elseif ($base64Image && $base64Image != $details->image) {
        //     // Handle image upload/change
        //     if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
        //         $imageType = strtolower($type[1]);

        //         if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
        //             return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
        //         }

        //         $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        //         $imageName = time() . '.' . $imageType;
        //         Storage::disk('public')->put('barangay_details/' . $imageName, $image);
        //     } else {
        //         return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
        //     }

        //     // Delete the old image from storage if a new one is uploaded
        //     if ($details->image) {
        //         $oldImageName = basename($details->image);
        //         if (Storage::disk('public')->exists('barangay_details/' . $oldImageName)) {
        //             Storage::disk('public')->delete('barangay_details/' . $oldImageName);
        //         }
        //     }
        // }

        // if ($base64Logo === null) {
        //     // Delete the old image from storage if it exists
        //     if ($details->logo) {
        //         $oldLogoName = basename($details->logo);
        //         if (Storage::disk('public')->exists('barangay_details/' . $oldLogoName)) {
        //             Storage::disk('public')->delete('barangay_details/' . $oldLogoName);
        //         }
        //     }
        // } elseif ($base64Logo && $base64Logo != $details->logo) {
        //     // Handle image upload/change
        //     if (preg_match('/^data:image\/(\w+);base64,/', $base64Logo, $type)) {
        //         $logoType = strtolower($type[1]);

        //         if (!in_array($logoType, ['jpg', 'jpeg', 'png'])) {
        //             return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
        //         }

        //         $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Logo));
        //         $logoName = time() . '.' . $logoType;
        //         Storage::disk('public')->put('barangay_details/' . $logoName, $image);
        //     } else {
        //         return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid Base64 string']);
        //     }

        //     // Delete the old image from storage if a new one is uploaded
        //     if ($details->logo) {
        //         $oldLogoName = basename($details->logo);
        //         if (Storage::disk('public')->exists('barangay_details/' . $oldLogoName)) {
        //             Storage::disk('public')->delete('barangay_details/' . $oldLogoName);
        //         }
        //     }
        // }


        // $details->update([
        //     'name' => $request->name,
        //     'image' => $imageName ? '/storage/barangay_details/' . $imageName : ($base64Image === null ? null : $details->image),
        //     'logo' => $logoName ? '/storage/barangay_details/' . $logoName : ($base64Logo === null ? null : $details->logo),
        // ]);


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
        // If no image is provided, delete the old image and return null
        if ($base64Image === null) {
            if ($currentImage) {
                $this->deleteOldImage($currentImage, $folder);
            }
            return null;
        }

        // If new image is the same as the current image, do nothing
        if ($base64Image === $currentImage) {
            return null;
        }

        // Process the new base64 image
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $imageType = strtolower($type[1]);

            // Validate image type
            if (!in_array($imageType, ['jpg', 'jpeg', 'png'])) {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['image' => 'Invalid image format']);
            }

            // Decode base64 image and generate unique image name with a prefix
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
            $imageName = $prefix . time() . '_' . uniqid() . '.' . $imageType;

            // Save the image
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
