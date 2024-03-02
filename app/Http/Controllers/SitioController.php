<?php

namespace App\Http\Controllers;

use App\Models\Sitio;
use App\Http\Controllers\Controller;
use App\Http\Requests\SitioRequest;
use App\Http\Resources\SitioResource;
use Illuminate\Http\Request;
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

        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => SitioResource::collection($sitios)
        ], 200);
    }

    public function store(SitioRequest $request)
    {
        Sitio::create([
            'id' => Str::uuid(),
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Sitio created successfully'
        ], 201);
    }

    public function show($id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);
        
        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data retrieved successfully',
            'data' => new SitioResource($sitio)
        ], 200);
    }


    public function update(SitioRequest $request, $id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        $sitio->name = $request->name;
        $sitio->save();

        return response()->json([
            'status' => 200,
            'message' => 'Sitio updated successfully',
            'data' => $sitio
        ], 200);
    }

    public function destroy($id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        $sitio->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Sitio deleted successfully'
        ], 200);
    }
}
