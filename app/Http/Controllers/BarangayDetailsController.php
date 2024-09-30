<?php

namespace App\Http\Controllers;

use App\Models\BarangayDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\BarangayDetailsRequest;
use App\Http\Resources\BarangayDetailsResource;
use Illuminate\Http\Request;

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

        $details->update([
            'name' => $request->name,
            'image' => $request->image,
            'logo' => $request->logo
        ]);
        return $this->jsonResponse(true, 200, 'Barangay Details updated successfully', $details);
    }
}
