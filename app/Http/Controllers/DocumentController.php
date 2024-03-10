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
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', DocumentResource::collection($document));
    }

    public function store(DocumentRequest $request)
    {
        Document::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'price' => $request->price
        ]);
        return $this->jsonResponse(true, 201, 'Document created successfully');
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
        $document = $this->findDataOrFail(Document::class, $id);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $document->update([
            'name' => $request->name,
            'price' => $request->price
        ]);
        return $this->jsonResponse(true, 200, 'Document updated successfully', $document);
    }

    public function destroy($id)
    {
        $document = $this->findDataOrFail(Document::class, $id);

        if ($document instanceof \Illuminate\Http\JsonResponse) {
            return $document;
        }

        $document->delete();
        return $this->jsonResponse(true, 200, 'Document deleted successfully');
    }
}
