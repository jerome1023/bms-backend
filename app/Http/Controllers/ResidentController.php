<?php

namespace App\Http\Controllers;

use App\Models\Resident;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Models\Sitio;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ResidentController extends Controller
{
    public function index()
    {
        $resident = Resident::where('archive_status', false)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', ResidentResource::collection($resident));
    }

    public function statistics()
    {
        $maleCount = $this->gender('Male');
        $femaleCount = $this->gender('Female');
        $seniorCitizenCount = Resident::whereDate('birthdate', '<=', Carbon::now()->subYears(60))->count();
        $voterCount = Resident::where('archive_status', false)
            ->where('voter_status', 'Voter')
            ->count();
        $householdCount = Resident::whereRaw('LOWER(house_number) != ?', ['none'])
            ->distinct('house_number')->count('house_number');

        $population = Sitio::withCount('residents')
            ->get()
            ->pluck('residents_count', 'name')
            ->toArray();
        $response = [
            'statistics' => [
                "Male" => $maleCount,
                "Female" => $femaleCount,
                "Senior Citizen" => $seniorCitizenCount,
                "Voter" => $voterCount,
                "Household" => $householdCount
            ],
            'population' => $population
        ];

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', $response);
    }

    public function archive_list()
    {
        $resident = Resident::where('archive_status', true)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', ResidentResource::collection($resident));
    }

    public function store(ResidentRequest $request)
    {
        if ($this->isExist($request)) {
            return $this->jsonResponse(false, 409, 'Resident with the same name already exists');
        }

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio, 'Sitio Not Found');

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

        return $this->jsonResponse(true, 201, 'Resident added successfully');
    }

    public function show($id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        return $this->jsonResponse(true, 201, 'Data retrieved successfully', new ResidentResource($resident));
    }

    public function update(ResidentRequest $request, $id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        if ($this->isExist($request, $id)) {
            return $this->jsonResponse(false, 409, 'Resident with the same name already exists');
        }

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio, 'Sitio Not Found');

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

        return $this->jsonResponse(true, 200, 'Resident updated successfully', $resident);
    }

    public function archive_status($id, $status)
    {
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $resident = $this->findDataOrFail(Resident::class, $id);
        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        $resident->archive_status = $status;
        $resident->save();

        $message = $status ? 'archive' : 'restore';
        return $this->jsonResponse(true, 200, "Resident {$message} successfully");
    }

    public function destroy($id)
    {
        $resident = $this->findDataOrFail(Resident::class, $id);

        if ($resident instanceof \Illuminate\Http\JsonResponse) {
            return $resident;
        }

        $resident->delete();
        return $this->jsonResponse(true, 200, 'Resident deleted successfully');
    }

    private function gender(string $type)
    {
        return Resident::where('archive_status', false)
            ->where('gender', $type)
            ->count();
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
