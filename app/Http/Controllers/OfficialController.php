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
        return $this->jsonResponse(true, 201, 'Data retrieved successfully', OfficialResource::collection($official));
    }

    public function store(OfficialRequest $request)
    {
        if ($this->isExist($request)) {
            return $this->jsonResponse(false, 409, 'Official with the same name already exists');
        }

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio_id, 'Sitio Not Found');

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

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio_id, 'Sitio Not Found');

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
        return $this->jsonResponse(true, 200, 'Official updated successfully', $official);
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
