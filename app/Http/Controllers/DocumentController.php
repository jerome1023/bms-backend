<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Http\Resources\DocumentResource;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function index()
    {
        $document = Document::all();
        return response()->json([
            'status' => 201,
            'message' => 'Data retrieved successfully',
            'data' => DocumentResource::collection($document)
        ], 200);
    }

    public function store(DocumentRequest $request)
    {
        Document::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'status' => 201,
            'message' => 'Document created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        //
    }

    public function update(DocumentRequest $request, $id)
    {
        $document = $this->findDataOrFail(Document::class ,$id);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $document->update([
            'name' => $request->name,
            'price' => $request->price
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Document updated successfully',
            'data' => $document
        ], 200);
    }

    public function destroy($id)
    {
        $document = $this->findDataOrFail(Document::class ,$id);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }
        
        $document->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Document deleted successfully'
        ], 200);
    }
}
