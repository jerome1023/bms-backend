<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Models\Sitio;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    public function index()
    {
        $resident = Resident::all();
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => ResidentResource::collection($resident)
        ], 200);
    }

    public function store(ResidentRequest $request)
    {
        if ($this->isExist($request)) {
            return response()->json([
                'status' => 409,
                'message' => 'Resident with the same name already exists'
            ]);
        }

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio_id, 'Sitio Not Found');

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        Resident::create([
            'id' => Str::uuid(),
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'birthplace' => $request->birthplace,
            'civil_status' => $request->civil_status,
            'religion' => $request->religion,
            'educational_attainment' => $request->educational_attainment,
            'sitio_id' => $sitio->id,
            'house_number' => $request->house_number,
            'occupation' => $request->occupation,
            'nationality' => $request->nationality,
            'voter_status' => $request->voter_status,
            'archive_status' => false
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Resident added successfully',
        ], 201);
    }

    public function show($id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => new ResidentResource($resident)
        ], 200);
    }

    public function update(ResidentRequest $request, $id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        if ($this->isExist($request, $id)) {
            return response()->json([
                'status' => 409,
                'message' => 'Resident with the same name already exists'
            ], 409);
        }

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio_id, 'Sitio Not Found');

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        $resident->update([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'birthplace' => $request->birthplace,
            'civil_status' => $request->civil_status,
            'religion' => $request->religion,
            'educational_attainment' => $request->educational_attainment,
            'sitio_id' => $sitio->id,
            'house_number' => $request->house_number,
            'occupation' => $request->occupation,
            'nationality' => $request->nationality,
            'voter_status' => $request->voter_status,
            'archive_status' => $request->archive_status ?? false,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Resident updated successfully',
            'data' => $resident
        ], 200);
    }

    public function destroy($id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        $resident->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Resident deleted successfully'
        ], 200);
    }

    private function isExist($request, $id = null)
    {
        $query = Resident::where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname);

        if ($id !== null) {
            $query->where('id', '<>', $id);
        }

        return $query->exists();
    }
}
