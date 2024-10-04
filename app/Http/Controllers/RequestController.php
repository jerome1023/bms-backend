<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request as RequestsRequest;
use App\Http\Resources\RequestResource;
use App\Models\Document;
use App\Models\Role;
use App\Models\Sitio;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($status, Authenticatable $user)
    {
        $allowedStatus = ['pending', 'approved', 'disapproved', 'completed'];

        if (!in_array($status, $allowedStatus)) {
            return $this->jsonResponse(false, 400, 'Invalid status');
        }

        $query = Request::where('status', $status)
            ->where('archive_status', false);

        if ($user->role->name !== "Administrator") {
            $query->where('user_id', $user->id);
        }

        $requests = $query->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', RequestResource::collection($requests));
    }

    public function archive_list($status)
    {
        $allowedStatus = ['pending', 'approved', 'disapproved', 'completed'];

        if (!in_array($status, $allowedStatus)) {
            return $this->jsonResponse(false, 400, 'Invalid status');
        }

        $requests = Request::where('status', $status)
            ->where('archive_status', false)->get();

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', RequestResource::collection($requests));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RequestsRequest $request, Authenticatable $user)
    {
        // $user = Auth::user();

        // dd($user->role->name);
        // $role = $this->findDataOrFail(Role::class, $user->role_id, 'Role Not Found');
        // if ($role instanceof \Illuminate\Http\JsonResponse) {
        //     return $role;
        // }

        if ($user->role->name !== 'User') {
            return $this->jsonResponse(false, 403, 'Unauthorized to request');
        }

        $document = $this->findDataOrFail(Document::class, $request->document, 'Document Not Found');
        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError) {
            return $validationError;
        }

        $price = $request->purpose !== 'School Requirement' ? $document->price : 0;

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio, 'Sitio Not Found');
        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        if ($this->isExist($request, $user, $document)) {
            return $this->jsonResponse(false, 400, 'Your request for the same document and name has not been completed yet');
        }

        Request::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'fullname' => $request->fullname,
            'age' => $request->age,
            'document_id' => $document->id,
            'purpose' => $request->purpose,
            'sitio_id' => $sitio->id,
            'income' => $request->income,
            'price' => $price,
            'status' => $request->status ?? 'pending',
            'archive_status' => false
        ]);
        return $this->jsonResponse(true, 201, 'Request document successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $request = $this->findDataOrFail(Request::class, $id);

        if ($request instanceof \Illuminate\Http\JsonResponse) {
            return $request;
        }

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', new RequestResource($request));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RequestsRequest $request, $id)
    {

        $user = Auth::user();

        // $role = $this->findDataOrFail(Role::class, $user->role_id, 'Role Not Found');
        // if ($role instanceof \Illuminate\Http\JsonResponse) {
        //     return $role;
        // }

        if ($user->role->name !== 'User') {
            return $this->jsonResponse(false, 403, 'Unauthorized to request');
        }

        $requestDocument = $this->findDataOrFail(Request::class, $id);
        if ($requestDocument instanceof \Illuminate\Http\JsonResponse) {
            return $requestDocument;
        }

        $document = $this->findDataOrFail(Document::class, $request->document, 'Document Not Found');
        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError) {
            return $validationError;
        }

        $price = $request->purpose !== 'School Requirement' ? $document->price : 0;

        $sitio = $this->findDataOrFail(Sitio::class, $request->sitio, 'Sitio Not Found');
        if ($sitio instanceof \Illuminate\Http\JsonResponse) {
            return $sitio;
        }

        if ($this->isExist($request, $user, $document, $id)) {
            return $this->jsonResponse(false, 400, 'Your request for the same document and name has not been completed yet');
        }

        $requestDocument->update([
            'fullname' => $request->fullname,
            'age' => $request->age,
            'document_id' => $document->id,
            'purpose' => $request->purpose,
            'sitio_id' => $sitio->id,
            'income' => $request->income,
            'price' => $price,
            'status' => $request->status ?? 'pending',
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(true, 201, 'Requested Document updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $request = $this->findDataOrFail(Request::class, $id);

        if ($request instanceof \Illuminate\Http\JsonResponse) {
            return $request;
        }

        $request->delete();

        return $this->jsonResponse(true, 200, 'Requested Document deleted successfully');
    }

    private function validateRequestDocument($request, $document)
    {
        $purpose = ['Work', 'School Requirement', 'Business', 'Others'];

        if (!in_array($request->purpose, $purpose)) {
            return $this->jsonResponse(false, 400, 'Invalid purpose');
        }

        if ($request->purpose == 'School Requirement') {
            $acceptedDocuments = ['Barangay Clearance', 'Barangay Residency', 'Barangay Certificate'];
            if (!in_array($document->name, $acceptedDocuments)) {
                return $this->jsonResponse(false, 400, 'The document is not accepted for school requirements');
            }
        }

        if ($request->purpose == 'Business') {
            $validator = Validator::make($request->all(), [
                'income' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse(false, 400, 'Validation error', null, $validator->errors());
            }
        }

        return null;
    }

    private function isExist($request, $user, $document, $id = null)
    {
        $query = Request::where('user_id', $user->id)
            ->where('fullname', $request->fullname)
            ->where('document_id', $document->id)
            ->where('purpose', $request->purpose)
            ->where('status', '!=', 'completed')
            ->where('archive_status', false);

        if ($id !== null) {
            $query->where('id', '<>', $id);
        }

        return $query->exists();
    }
}
