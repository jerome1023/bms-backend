<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Document;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function index(Authenticatable $user)
    {
        if ($user->role->name === 'Administrator') {
            $transaction = Transaction::where('archive_status', false)
                ->orderBy('created_at')
                ->get();
        } else {
            $transaction = Transaction::where('archive_status', false)
                ->where('user_id', $user->id)
                ->orderBy('created_at')
                ->get();
        }

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', TransactionResource::collection($transaction));
    }

    public function archive_list()
    {
        $transaction = Transaction::where('archive_status', true)->get();
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', TransactionResource::collection($transaction));
    }

    public function store(TransactionRequest $request)
    {
        $document = $this->findDataOrFail(Document::class, $request->document);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError) {
            return $validationError;
        }

        $price = 0;
        if ($request->purpose != 'School Requirement') {
            $price = $document->price;
        }

        Transaction::create([
            'id' => Str::uuid(),
            'fullname' => $request->fullname,
            'user_id' => $request->user_id,
            'document_id' => $document->id,
            'purpose' => $request->purpose,
            'price' => $price,
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(true, 201, 'Transaction created successfully');
    }

    public function show($id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        return $this->jsonResponse(true, 201, 'Data retrieved successfully', new TransactionResource($transaction));
    }

    public function update(TransactionRequest $request, $id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        $document = $this->findDataOrFail(Document::class, $request->document);
        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $validationError = $this->validateRequestDocument($request, $document);
        if ($validationError) {
            return $validationError;
        }

        $price = 0;
        if ($request->purpose != 'School Requirement') {
            $price = $document->price;
        }

        $transaction->update([
            'fullname' => $request->fullname,
            'document_id' => $request->document,
            'purpose' => $request->purpose,
            'price' => $price,
            'archive_status' => $request->archive_status ?? false
        ]);

        return $this->jsonResponse(true, 201, 'Transaction updated successfully', $transaction);
    }

    public function archive_status($id, $status)
    {
        $status = filter_var($status, FILTER_VALIDATE_BOOLEAN);

        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        $transaction->archive_status = $status;
        $transaction->save();

        $message = $status ? 'archive' : 'restore';
        return $this->jsonResponse(true, 200, "Transaction {$message} successfully");
    }


    public function destroy($id)
    {
        $transaction = $this->findDataOrFail(Transaction::class, $id);
        if ($transaction instanceof \Illuminate\Http\JsonResponse) {
            return $transaction;
        }

        $transaction->delete();
        return $this->jsonResponse(true, 200, 'Resident deleted successfully');
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

        // if ($request->purpose == 'Business') {
        //     $validator = Validator::make($request->all(), [
        //         'price' => 'required|integer'
        //     ]);

        //     if ($validator->fails()) {
        //         return $this->jsonResponse(false, 400, 'Validation error', null, $validator->errors());
        //     }
        // }

        return null;
    }
}
