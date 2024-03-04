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

        return $this->jsonResponse(200, 'Data retrieved successfully', SitioResource::collection($sitios));
        
    }

    public function store(SitioRequest $request)
    {
        Sitio::create([
            'id' => Str::uuid(),
            'name' => $request->name
        ]);

        return $this->jsonResponse(201, 'Sitio created successfully');

    }

    public function show($id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);
        
        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        return $this->jsonResponse(200, 'Data retrieved successfully', new SitioResource($sitio));

    }


    public function update(SitioRequest $request, $id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        $sitio->name = $request->name;
        $sitio->save();

        return $this->jsonResponse(200, 'Sitio updated successfully', $sitio);

    }

    public function destroy($id)
    {
        $sitio = $this->findDataOrFail(Sitio::class ,$id);

        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        $sitio->delete();

        return $this->jsonResponse(200, 'Sitio deleted successfully');

    }
}
