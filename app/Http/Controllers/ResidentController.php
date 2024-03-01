<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResidentResource;
use App\Models\Sitio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    public function index()
    {
        $resident = Resident::all();
        $data = ResidentResource::collection($resident);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function view($name)
    {
        $resident = Resident::where('lastname', $name)->get();
        if ($resident->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found'
            ], 404);
        }

        $residentResource = ResidentResource::collection($resident);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $residentResource
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|max:255',
            'civil_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'educational_attainment' => 'required|string|max:255',
            'sitio_id' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'voter_status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $existingResident = Resident::where([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
        ])->exists();

        if ($existingResident) {
            return response()->json([
                'status' => 409,
                'message' => 'Resident with the same name already exists'
            ], 409);
        }

        $sitio = Sitio::where('id', $request->sitio_id)->first();

        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
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

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'birthplace' => 'required|string|max:255',
            'civil_status' => 'required|string|max:255',
            'religion' => 'required|string|max:255',
            'educational_attainment' => 'required|string|max:255',
            'sitio_id' => 'required|string|max:255',
            'house_number' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'voter_status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $resident = Resident::where('id', $id)->first();
        if (!$resident) {
            return response()->json([
                'status' => 404,
                'message' => 'Resident not found'
            ], 404);
        }
        
        $exists = DB::table('resident')
            ->where('id', '<>', $id)
            ->where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 400,
                'message' => 'Resident name already exists.'
            ], 400);
        }

        $sitio = Sitio::where('id', $request->sitio_id)->first();

        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
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
        $resident = Resident::where('id', $id)->first();
        if (!$resident) {
            return response()->json([
                'status' => 404,
                'message' => 'Resident not found'
            ], 404);
        }

        $resident->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Resident deleted successfully'
        ], 200);
    }
}
