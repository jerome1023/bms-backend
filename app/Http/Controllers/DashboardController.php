<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\Document;
use App\Models\Request as ModelsRequest;
use App\Models\Resident;

class DashboardController extends Controller
{
    public function index()
    {
        $resident = Resident::where('archive_status', false)->count();
        $pending = ModelsRequest::where('status', 'pending')
            ->where('archive_status', false)->count();
        $approved = ModelsRequest::where('status', 'approved')
            ->where('archive_status', false)->count();
        $blotter = Blotter::where('archive_status', false)->count();

        $documents = Document::orderBy('created_at')->get()->map(function($document) {
            return [
                'code' => $document->id,
                'name' => $document->name,
            ];
        });

        return $this->jsonResponse(true, 200, 'Data retrieved successfully', ['resident' => $resident, 'pending' => $pending, 'approved' => $approved, 'blotter' => $blotter, 'documents' => $documents]);
    }
}
