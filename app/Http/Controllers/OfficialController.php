<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfficialResource;
use App\Models\Sitio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OfficialController extends Controller
{
    public function index()
    {
        $official = Official::all();
        $data = OfficialResource::collection($official);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function view($name)
    {
        $officialName = Official::where('lastname', $name)->get();
        if ($officialName->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No data found'
            ], 404);
        }

        $officialResource = OfficialResource::collection($officialName);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $officialResource
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'sitio_id' => 'required|string|max:255',
            'start_term' => 'required|date',
            'end_term' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $existingOfficial = Official::where([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
        ])->exists();

        if ($existingOfficial) {
            return response()->json([
                'status' => 409,
                'message' => 'Official with the same name already exists'
            ], 409);
        }

        $sitio = Sitio::where('id', $request->sitio_id)->first();

        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
        }

        Official::create([
            'id' => Str::uuid(),
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'position' => $request->position,
            'birthdate' => $request->birthdate,
            'sitio_id' => $sitio->id,
            'start_term' => $request->start_term,
            'end_term' => $request->end_term,
            'archive_status' => false
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Official added successfully',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'middlename' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'birthdate' => 'required|date',
            'sitio_id' => 'required|string|max:255',
            'start_term' => 'required|date',
            'end_term' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }
        
        $official = Official::where('id', $id)->first();
        if (!$official) {
            return response()->json([
                'status' => 404,
                'message' => 'Official not found'
            ], 404);
        }
        
        $exists = DB::table('official')
            ->where('id', '<>', $id)
            ->where('firstname', $request->firstname)
            ->where('middlename', $request->middlename)
            ->where('lastname', $request->lastname)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 400,
                'message' => 'Official name already exists.'
            ], 400);
        }

        $sitio = Sitio::where('id', $request->sitio_id)->first();

        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
        }

        $official->update([
            'firstname' => $request->firstname,
            'middlename' => $request->middlename,
            'lastname' => $request->lastname,
            'gender' => $request->gender,
            'position' => $request->position,
            'birthdate' => $request->birthdate,
            'sitio_id' => $sitio->id,
            'start_term' => $request->start_term,
            'end_term' => $request->end_term,
            'archive_status' => $request->archive_status ?? false,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Official updated successfully',
            'data' => $official
        ], 200);
    }

    public function destroy($id)
    {
        $official = Official::where('id', $id)->first();
        if (!$official) {
            return response()->json([
                'status' => 404,
                'message' => 'Official not found'
            ], 404);
        }

        $official->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Official deleted successfully'
        ], 200);
    }
}
