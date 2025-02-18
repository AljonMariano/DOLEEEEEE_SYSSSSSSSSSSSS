<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get recent records for the Records section
        $recentRecords = Record::with('creator')
            ->latest()
            ->take(5)
            ->get();

        // Define the main sections
        $sections = [
            [
                'name' => 'RECORDS',
                'description' => 'Document tracking and routing system',
                'icon' => 'document-text',
                'route' => 'records.index',
                'count' => Record::count(),
                'color' => 'blue'
            ],
            [
                'name' => 'BUDGET',
                'description' => 'Budget management and tracking',
                'icon' => 'cash',
                'route' => 'budget.index',
                'count' => 0, // Will be implemented later
                'color' => 'green'
            ],
            [
                'name' => 'ACCOUNTING',
                'description' => 'Accounting records and management',
                'icon' => 'calculator',
                'route' => 'accounting.index',
                'count' => 0, // Will be implemented later
                'color' => 'yellow'
            ],
            [
                'name' => 'CASHIER',
                'description' => 'Cashier operations and tracking',
                'icon' => 'currency-dollar',
                'route' => 'cashier.index',
                'count' => 0, // Will be implemented later
                'color' => 'red'
            ]
        ];

        return view('dashboard', compact('recentRecords', 'sections'));
    }
} 