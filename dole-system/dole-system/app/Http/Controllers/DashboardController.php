<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get document statistics
        $totalDocuments = Document::count();
        $pendingDocuments = Document::where('status', 'pending')->count();
        $completedDocuments = Document::where('status', 'completed')->count();
        $todayDocuments = Document::whereDate('created_at', Carbon::today())->count();

        $sections = [
            [
                'name' => 'Total Documents',
                'count' => $totalDocuments,
                'color' => 'blue',
                'icon' => 'document-text'
            ],
            [
                'name' => 'Pending Documents',
                'count' => $pendingDocuments,
                'color' => 'yellow',
                'icon' => 'clock'
            ],
            [
                'name' => 'Completed Documents',
                'count' => $completedDocuments,
                'color' => 'green',
                'icon' => 'check-circle'
            ],
            [
                'name' => 'Today\'s Documents',
                'count' => $todayDocuments,
                'color' => 'indigo',
                'icon' => 'calendar'
            ]
        ];

        // Get recent documents
        $recentDocuments = Document::with('routes')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('sections', 'recentDocuments'));
    }
}
