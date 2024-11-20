<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfficialRequest;
use App\Http\Resources\OfficialResource;
use Illuminate\Support\Str;

class OfficialController extends Controller
{
    public function index()
    {
        $official = Official::where('archive_status', false)
            ->orderByRaw('
                CASE 
                    WHEN position = \'Punong Barangay\' THEN 1
                    WHEN position = \'Kagawad\' THEN 2
                    WHEN position = \'Sk Chairman\' THEN 3
                    WHEN position = \'Kalihim\' THEN 4
                    WHEN position = \'Ingat Yaman\' THEN 5
                    ELSE 6
                END
            ')
            ->get();
        return $this->jsonResponse(true, 201, 'Data retrieved successfully', OfficialResource::collection($official));
    }

    public function archive_list()
    {
        $blotters = Official::where('archive_status', true)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', OfficialResource::collection($blotters));
    }

    public function store(OfficialRequest $request)
    {
        if ($this->isExist($request)) {
            return $this->jsonResponse(false, 409, 'Official with the same name already exists');
        }

        Official::create([
            'id' => Str::uuid(),
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'position' => $request->position,
            'birthdate' => $request->birthdate,
            'start_term' => $request->start_term,
            'end_term' => $request->end_term,
            'archive_status' => false
        ]);
        return $this->jsonResponse(true, 201, 'Official added successfully');
    }

    public function show($id)
    {
        $official = $this->findDataOrFail(Official::class, $id);

        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }
        return $this->jsonResponse(true, 201, 'Data retrieved successfully', new OfficialResource($official));
    }

    public function update(OfficialRequest $request, $id)
    {
        $official = $this->findDataOrFail(Official::class, $id);

        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        if ($this->isExist($request, $id)) {
            return $this->jsonResponse(false, 409, 'Official with the same name already exists');
        }

        $official->update([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'position' => $request->position,
            'birthdate' => $request->birthdate,
            'start_term' => $request->start_term,
            'end_term' => $request->end_term,
            'archive_status' => $request->archive_status ?? false,
        ]);
        return $this->jsonResponse(true, 200, 'Official updated successfully', $official);
    }

    public function archive_status($id, $status)
    {
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $official = $this->findDataOrFail(Official::class, $id);
        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        $official->archive_status = $status;
        $official->save();

        $message = $status ? 'archive' : 'restore';
        return $this->jsonResponse(true, 200, "Official {$message} successfully");
    }

    public function destroy($id)
    {
        $official = $this->findDataOrFail(Official::class, $id);

        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        $official->delete();
        return $this->jsonResponse(true, 200, 'Official deleted successfully');
    }

    private function isExist($request, $id = null)
    {
        $query = Official::where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname);

        if ($id !== null) {
            $query->where('id', '<>', $id);
        }

        return $query->exists();
    }
}
