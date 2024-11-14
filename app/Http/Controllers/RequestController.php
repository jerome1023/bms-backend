<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request as RequestsRequest;
use App\Http\Resources\RequestResource;
use App\Models\Document;
use App\Models\Sitio;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function index($status, Authenticatable $user)
    {
        $allowedStatus = ['pending', 'approved', 'disapproved', 'completed'];

        if (!in_array($status, $allowedStatus)) {
            return $this->jsonResponse(false, 400, 'Invalid status');
        }

        $query = Request::where('status', $status)
            ->where('archive_status', false)
            ->orderBy('created_at');

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
            ->where('archive_status', true)->get();

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', RequestResource::collection($requests));
    }

    public function store(RequestsRequest $request, Authenticatable $user)
    {

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

    public function show($id)
    {
        $request = $this->findDataOrFail(Request::class, $id);

        if ($request instanceof \Illuminate\Http\JsonResponse) {
            return $request;
        }

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', new RequestResource($request));
    }

    public function update(RequestsRequest $request, $id)
    {

        $user = Auth::user();

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

    public function updateStatus(HttpRequest $request, $id, $status)
    {
        $requestDocument = $this->findDataOrFail(Request::class, $id);
        if ($requestDocument instanceof \Illuminate\Http\JsonResponse) {
            return $requestDocument;
        }

        if ($status === 'disapproved') {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse(false, 400, 'Validation error', null, $validator->errors());
            }

            $requestDocument->reason = $request->reason;
        }
        $requestDocument->status = $status;
        $requestDocument->date = Carbon::now();
        $requestDocument->save();

        return $this->jsonResponse(true, 201, "Requested document {$status} successfully");
    }

    public function complete($id)
    {
        $requestDocument = $this->findDataOrFail(Request::class, $id);
        if ($requestDocument instanceof \Illuminate\Http\JsonResponse) {
            return $requestDocument;
        }

        $document = $this->findDataOrFail(Document::class, $requestDocument->document_id);
        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $price = 0;
        if ($requestDocument->purpose != 'School Requirement') {
            $price = $document->price;
        }

        Transaction::create([
            'id' => Str::uuid(),
            'fullname' => $requestDocument->fullname,
            'user_id' => $requestDocument->user_id,
            'document_id' => $document->id,
            'purpose' => $requestDocument->purpose,
            'price' => $price,
            'archive_status' => $request->archive_status ?? false
        ]);

        $requestDocument->status = "completed";
        $requestDocument->save();

        return $this->jsonResponse(true, 201, "Requested document complete successfully");
    }

    public function archive_status($id, $status)
    {
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $requestDetails = $this->findDataOrFail(Request::class, $id);
        if ($requestDetails instanceof \Illuminate\Http\JsonResponse) {
            return $requestDetails;
        }

        $requestDetails->archive_status = $status;
        $requestDetails->save();

        $message = $status ? 'archive' : 'restore';
        return $this->jsonResponse(true, 200, "Request {$message} successfully");
    }

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
        $businessDocuments = ['Business Clearance (A)', 'Business Clearance (B)', 'Business Clearance (C)', 'Business Clearance (D)'];

        if (!in_array($request->purpose, $purpose)) {
            return $this->jsonResponse(false, 400, 'Invalid purpose');
        }

        if ($request->purpose == 'School Requirement') {
            $acceptedDocuments = ['Barangay Clearance', 'Barangay Residency', 'Barangay Certificate'];
            if (!in_array($document->name, $acceptedDocuments)) {
                return $this->jsonResponse(false, 400, 'Validation error', null, ['document' => ["The document is not accepted for school requirements"]]);
            }
        } elseif ($request->purpose == 'Business') {
            $income = $request->income;
            $businessClearances = [
                'Business Clearance (A)' => [0, 50000],
                'Business Clearance (B)' => [50000, 100000],
                'Business Clearance (C)' => [100000, 500000],
                'Business Clearance (D)' => [500000, PHP_INT_MAX],
            ];

            if(in_array($document->name, $businessDocuments)){
                $validator = Validator::make($request->all(), [
                    'income' => 'required|integer'
                ]);
    
                if ($validator->fails()) {
                    return $this->jsonResponse(false, 400, 'Validation error', null, $validator->errors());
                }
            }
            
            // Check if the selected document matches the income range
            $correctDocument = null;
            foreach ($businessClearances as $clearanceName => [$minIncome, $maxIncome]) {
                if ($income >= $minIncome && $income <= $maxIncome) {
                    $correctDocument = $clearanceName;
                    break;
                }
            }
            // Check if the selected document matches the income range
            if (array_key_exists($document->name, $businessClearances)) {
                [$minIncome, $maxIncome] = $businessClearances[$document->name];
                if ($income < $minIncome || $income > $maxIncome) {
                    return $this->jsonResponse(false, 400, "Validation error", null, [
                        'document' => ["The selected document does not match the income range. The correct document for your income is: $correctDocument"]
                    ]);
                }
            } 
        }
        
        if(in_array($document->name, $businessDocuments) && $request->purpose != "Business" ){
            return $this->jsonResponse(false, 400, "Validation error", null, [
                'purpose' => ["Business Clearance is for Business purpose only"]
            ]);
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
