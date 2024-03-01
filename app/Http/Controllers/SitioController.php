<?php

namespace App\Http\Controllers;

use App\Models\Sitio;
use App\Http\Controllers\Controller;
use App\Http\Resources\SitioResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SitioController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('name')) {
            $sitios = Sitio::where('name', $request->name)->get();
        } else {
            $sitios = Sitio::all();
        }

        $sitiosResource = SitioResource::collection($sitios);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $sitiosResource
        ], 200);
    }

    public function view($name)
    {
        $sitioName = Sitio::where('name', $name)->get();
        if($sitioName->isEmpty()){
            return response()->json([
                'status' => 404,
                'message' => 'No data found'
            ], 404);
        }

        $sitiosResource = SitioResource::collection($sitioName);
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => $sitiosResource
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $existingSitio = Sitio::where('name', $request->name)->exists();
        if ($existingSitio) {
            return response()->json([
                'status' => 400,
                'message' => 'The name has already been taken'
            ], 400);
        }

        Sitio::create([
            'id' => Str::uuid(),
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Sitio created successfully'
        ], 201);
    }

    public function update(Request $request, $name)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $sitio = Sitio::where('name', $name)->first();
        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
        }

        $sitio->name = $request->name;
        $sitio->save();

        return response()->json([
            'status' => 200,
            'message' => 'Sitio updated successfully',
            'data' => $sitio
        ], 200);
    }

    public function destroy($name)
    {
        $sitio = Sitio::where('name', $name)->first();

        if (!$sitio) {
            return response()->json([
                'status' => 404,
                'message' => 'Sitio not found'
            ], 404);
        }

        $sitio->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Sitio deleted successfully'
        ], 200);
    }
}
