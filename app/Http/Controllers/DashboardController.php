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
        $now = Carbon::now();
        $docs = [];

        $resident = Resident::where('archive_status', false)->count();
        $pending = $this->countRequestsByStatus('pending');
        $approved = $this->countRequestsByStatus('approved');
        $blotter = Blotter::where('archive_status', false)->count();

        $docs['list'] = $this->getDocumentList();

        if ($firstDocument = Document::orderBy('created_at', 'asc')->first()) {
            $docs = array_merge($docs, $this->document($firstDocument->id, $now->year, $now->month));
        }

        $response = [
            'resident' => $resident,
            'pending' => $pending,
            'approved' => $approved,
            'blotter' => $blotter,
            'docs' => $docs,
            'total_revenue' => $this->revenue($now->year, $now->month)
        ];
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', $response);
    }

    public function search(Request $request)
    {
        $response = [];
        [$currentYear, $currentMonth] = explode('-', $request->year_month);

        if ($request->document) {
            $response['docs'] = $this->document($request->document, $currentYear, $currentMonth);
        } else {
            $response['revenue'] = $this->revenue($currentYear, $currentMonth);
        }
        return $this->jsonResponse(true, 200, 'Data retrieved successfully', $response);
    }

    private function countRequestsByStatus(string $status): int
    {
        return ModelsRequest::where('status', $status)
            ->where('archive_status', false)->count();
    }

    private function getDocumentList(): array
    {
        return Document::orderBy('created_at', 'asc')->get()->map(function ($document) {
            return [
                'code' => $document->id,
                'name' => $document->name,
            ];
        })->toArray();
    }

    private function document(string $docs_id, int $year, int $month): array
    {
        $docs = Transaction::where('document_id', $docs_id)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('archive_status', false)
            ->selectRaw('SUM(price) as revenue, COUNT(*) as count')
            ->first();

        return [
            'revenue' => $docs->revenue ?? 0,
            'count' => $docs->count ?? 0,
        ];
    }

    private function revenue(int $year, int $month): float
    {
        return Transaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('archive_status', false)
            ->sum('price') ?? 0.0;
    }
}
