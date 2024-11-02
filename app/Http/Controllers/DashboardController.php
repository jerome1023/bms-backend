<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\Document;
use App\Models\Request as ModelsRequest;
use App\Models\Resident;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

        $document_list = Document::orderBy('created_at', 'asc')->get()->map(function ($document) {
            return [
                'code' => $document->id,
                'name' => $document->name,
            ];
        });

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $revenue = Transaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('archive_status', false)
            ->sum('price');

        $firstDocument = Document::orderBy('created_at', 'asc')->first();
        $docs = (object) [
            'revenue' => 0,
            'count' => 0,
        ];

        if ($firstDocument) {
            $docs->revenue = Transaction::where('document_id', $firstDocument->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('archive_status', false)
                ->sum('price'); // This gives you the total revenue

            $docs->count = Transaction::where('document_id', $firstDocument->id)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('archive_status', false)
                ->count(); // This gives you the number of transactions
        }

        $response = [
            'resident' => $resident,
            'pending' => $pending,
            'approved' => $approved,
            'blotter' => $blotter,
            'document_list' => $document_list,
            'docs' => $docs,
            'revenue' => $revenue
        ];
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', $response);
    }

    public function search(Request $request)
    {
        $response = [];
        [$currentYear, $currentMonth] = explode('-', $request->year_month);

        if ($request->document) {
            $docs = Transaction::where('document_id', $request->document)
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('archive_status', false)
                ->selectRaw('SUM(price) as revenue, COUNT(*) as count')
                ->first();

            $response['docs_revenue'] = $docs->revenue ?? 0;
            $response['docs_count'] = $docs->count ?? 0;
        } else {
            $response['revenue'] = Transaction::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('archive_status', false)
                ->sum('price');
        }
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', $response);
    }
}
