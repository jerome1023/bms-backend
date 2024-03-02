<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Http\Controllers\Controller;
use App\Http\Requests\OfficialRequest;
use App\Http\Resources\OfficialResource;
use App\Models\Sitio;
use Illuminate\Support\Str;

class OfficialController extends Controller
{
    public function index()
    {
        $official = Official::all();
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => OfficialResource::collection($official)
        ], 200);
    }

    public function store(OfficialRequest $request)
    {
        if ($this->isExist($request)) {
            return response()->json([
                'status' => 409,
                'message' => 'Official with the same name already exists'
            ], 409);
        }

        $sitio = $this->findDataOrFail(Sitio::class ,$request->sitio_id, 'Sitio Not Found');

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
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

    public function show($id)
    {
        $official = $this->findDataOrFail(Official::class ,$id);

        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => new OfficialResource($official)
        ], 200);
    }

    public function update(OfficialRequest $request, $id)
    {    
        $official = $this->findDataOrFail(Official::class ,$id);

        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        if ($this->isExist($request, $id)) {
            return response()->json([
                'status' => 409,
                'message' => 'Official with the same name already exists'
            ], 409);
        }

        $sitio = $this->findDataOrFail(Sitio::class ,$request->sitio_id, 'Sitio Not Found');

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
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
        $official = $this->findDataOrFail(Official::class ,$id);
        
        if ($official instanceof \Illuminate\Http\JsonResponse) {
            return $official;
        }

        $official->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Official deleted successfully'
        ], 200);
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
