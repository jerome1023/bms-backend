<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Http\Controllers\Controller;
use App\Http\Resources\OfficialResource;
use App\Models\Sitio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OfficialController extends Controller
{
    public function index(Request $request)
    {
        $official = Official::all();
        $data = OfficialResource::collection($official);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
                'message' => 'An official with the same name already exists'
            ], 409);
        }

        $sitio = Sitio::where('id', $request->sitio_id)->first();

        if (!$sitio) {
            return response()->json([
                'status' => false,
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

    /**
     * Display the specified resource.
     */
    public function show(Official $official)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Official $official)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Official $official)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Official $official)
    {
        //
    }
}
