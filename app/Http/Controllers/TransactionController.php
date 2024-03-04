<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = Transaction::all();
        return $this->jsonResponse(200, 'Data retrieved successfully', TransactionResource::collection($transaction));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransactionRequest $request)
    {
        $document = $this->findDataOrFail(Document::class, $request->document_id);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError ) {
            return $validationError;
        }

        Transaction::create([
            'id' => Str::uuid(),
            'fullname' => $request->fullname,
            'user_id' => $request->user_id,
            'document_id' => $request->document_id,
            'purpose' => $request->purpose,
            'price' => $request->price,
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(201, 'Transaction created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        return $this->jsonResponse(201, 'Data retrieved successfully', new TransactionResource($transaction));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransactionRequest $request, $id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        $document = $this->findDataOrFail(Document::class, $request->document_id);
        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError ) {
            return $validationError;
        }

        $transaction->update([
            'fullname' => $request->fullname,
            'document_id' => $request->document_id,
            'purpose' => $request->purpose,
            'price' => $request->price,
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(201, 'Transaction updated successfully', $transaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        $transaction->delete();
        return $this->jsonResponse(200, 'Resident deleted successfully');
    }

    private function validateRequestDocument($request, $document)
    {
        $purpose = ['work', 'school_requirement', 'business', 'others'];

        if (!in_array($request->purpose, $purpose)) {
            return $this->jsonResponse(400, 'Invalid purpose');
        }

        if ($request->purpose == 'school_requirement') {
            $acceptedDocuments = ['Barangay Clearance', 'Barangay Residency', 'Barangay Certificate'];
            if (!in_array($document->name, $acceptedDocuments)) {
                return $this->jsonResponse(400, 'The document is not accepted for school requirements');
            }

        }

        if ($request->purpose == 'business') {
            $validator = Validator::make($request->all(), [
                'price' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->jsonResponse(400, 'Validation error', null, $validator->errors());
            }
        }

        return null;
    }
}
