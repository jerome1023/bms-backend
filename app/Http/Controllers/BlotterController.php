<?php

namespace App\Http\Controllers;

use App\Models\Blotter;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlotterRequest;
use App\Http\Resources\BlotterResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlotterController extends Controller
{
    public function index()
    {
        $blotters = Blotter::where('archive_status', false)
            ->orderBy('created_at')
            ->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', BlotterResource::collection($blotters));
    }

    public function archive_list()
    {
        $blotters = Blotter::where('archive_status', true)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', BlotterResource::collection($blotters));
    }

    public function store(BlotterRequest $request)
    {
        Blotter::create([
            'id' => Str::uuid(),
            "complainant" => $request->complainant,
            "complainant_age" => $request->complainant_age,
            "complainant_address" => $request->complainant_address,
            "complainant_contact_number" => str_replace('-', '', $request->complainant_contact_number),
            "complainee" => $request->complainee,
            "complainee_age" => $request->complainee_age,
            "complainee_address" => $request->complainee_address,
            "complainee_contact_number" => str_replace('-', '', $request->complainee_contact_number),
            "date" => $request->date,
            "complain" => $request->complain,
            "status" => $request->status ?? 'unsolve',
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(true, 201, 'Blotter created successfully');
    }

    public function show($id)
    {
        $blotter = $this->findDataOrFail(Blotter::class, $id);

        if ($blotter instanceof \Illuminate\Http\JsonResponse) {
            return $blotter;
        }

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', new BlotterResource($blotter));
    }

    public function update(BlotterRequest $request, $id)
    {
        $blotter = $this->findDataOrFail(Blotter::class, $id);
        if ($blotter instanceof \Illuminate\Http\JsonResponse) {
            return $blotter;
        }

        $blotter->update([
            "complainant" => $request->complainant,
            "complainant_age" => $request->complainant_age,
            "complainant_address" => $request->complainant_address,
            "complainant_contact_number" => str_replace('-', '', $request->complainant_contact_number),
            "complainee" => $request->complainee,
            "complainee_age" => $request->complainee_age,
            "complainee_address" => $request->complainee_address,
            "complainee_contact_number" => str_replace('-', '', $request->complainant_contact_number),
            "date" => $request->date,
            "complain" => $request->complain,
            "status" => $request->status,
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(true, 200, 'Blotter updated successfully', $blotter);
    }

    public function solve(Request $request, $id)
    {
        $blotter = $this->findDataOrFail(Blotter::class, $id);
        if ($blotter instanceof \Illuminate\Http\JsonResponse) {
            return $blotter;
        }

        $validator = Validator::make($request->all(), [
            'agreement' => 'required|string|max:255',
            'namagitan' => 'required|string|max:255',
            'witness' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 400, 'Validation error', null, $validator->errors());
        }

        $blotter->update([
            "agreement" => $request->agreement,
            "namagitan" => $request->namagitan,
            "witness" => $request->witness,
            "status" => "solve",
        ]);

        return $this->jsonResponse(true, 200, 'Blotter solved successfully', $blotter);
    }

    public function archive_status($id, $status)
    {
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $blotter = $this->findDataOrFail(Blotter::class, $id);
        if ($blotter instanceof \Illuminate\Http\JsonResponse) {
            return $blotter;
        }

        $blotter->archive_status = $status;
        $blotter->save();

        $message = $status ? 'archive' : 'restore';
        return $this->jsonResponse(true, 200, "Blotter {$message} successfully");
    }

    public function destroy($id)
    {
        $blotter = $this->findDataOrFail(Blotter::class, $id);

        if ($blotter instanceof \Illuminate\Http\JsonResponse) {
            return $blotter;
        }

        $blotter->delete();

        return $this->jsonResponse(true, 200, 'Blotter deleted successfully');
    }
}
