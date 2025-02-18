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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documents = Document::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $departments = Department::all();

        return view('records.index', [
            'documents' => $documents,
            'departments' => $departments
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::all();
        $newDocId = Document::generateDocId();
        
        return view('records.create', compact('departments', 'newDocId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'forward_to' => 'required|exists:departments,code',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $document = Document::create([
            'doc_id' => Document::generateDocId(),
            'date_received' => now(),
            'time_received' => now(),
            'origin' => $validated['origin'],
            'subject' => $validated['subject'],
            'forward_to' => $validated['forward_to'],
            'remarks' => $validated['remarks'],
            'status' => 'pending'
        ]);

        // Create initial route
        $document->routeTo($validated['forward_to']);

        return redirect()->route('records.index')
            ->with('success', 'Document created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load('routes');
        $departments = Department::all();
        
        return view('records.show', compact('document', 'departments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
