<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecordsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $documents = Document::with('routes')
            ->orderBy('date_received', 'desc')
            ->orderBy('time_received', 'desc')
            ->paginate(15);

        $departments = Department::all();

        return view('records.index', compact('documents', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $newDocId = Document::generateDocId();
        
        return view('records.create', compact('departments', 'newDocId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'subject' => 'required|string',
            'forward_to' => 'required|exists:departments,code',
            'remarks' => 'nullable|string',
        ]);

        $document = Document::create([
            'doc_id' => Document::generateDocId(),
            'date_received' => Carbon::now()->toDateString(),
            'time_received' => Carbon::now()->toTimeString(),
            'origin' => $validated['origin'],
            'subject' => $validated['subject'],
            'forward_to' => $validated['forward_to'],
            'remarks' => $validated['remarks'],
            'status' => 'pending'
        ]);

        // Create initial route
        $document->routeTo($validated['forward_to']);

        return redirect()->route('records.show', $document)
            ->with('success', 'Document created successfully.');
    }

    public function show(Document $document)
    {
        $document->load('routes');
        $departments = Department::all();
        
        return view('records.show', compact('document', 'departments'));
    }

    public function route(Request $request, Document $document)
    {
        $validated = $request->validate([
            'to_department' => 'required|exists:departments,code',
            'notes' => 'nullable|string',
        ]);

        $document->routeTo($validated['to_department'], $validated['notes']);

        return redirect()->back()->with('success', 'Document routed successfully.');
    }

    public function receive(Request $request, Document $document)
    {
        $validated = $request->validate([
            'department' => 'required|exists:departments,code',
            'notes' => 'nullable|string',
        ]);

        $document->markAsReceived($validated['department'], $validated['notes']);

        return redirect()->back()->with('success', 'Document received successfully.');
    }

    public function search(Request $request)
    {
        $query = Document::query();

        if ($request->filled('doc_id')) {
            $query->where('doc_id', 'like', '%' . $request->doc_id . '%');
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('date_received', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date_received', '<=', $request->date_to);
        }

        if ($request->filled('department')) {
            $query->whereHas('routes', function ($q) use ($request) {
                $q->where('to_department', $request->department);
            });
        }

        $documents = $query->with('routes')
            ->orderBy('date_received', 'desc')
            ->orderBy('time_received', 'desc')
            ->paginate(15);

        $departments = Department::all();

        return view('records.index', compact('documents', 'departments'));
    }
} 