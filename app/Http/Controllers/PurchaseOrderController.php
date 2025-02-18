<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderRoute;
use App\Models\PurchaseRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::query();

        // Apply filters
        if ($request->filled('po_id')) {
            $query->where('po_id', 'like', '%' . $request->po_id . '%');
        }
        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }
        if ($request->filled('department')) {
            $query->where('forward_to', $request->department);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('supplier')) {
            $query->where('supplier', 'like', '%' . $request->supplier . '%');
        }

        $purchaseOrders = $query->latest('date')->latest('time')->paginate(10);
        $departments = Department::all();

        return view('records.po.index', compact('purchaseOrders', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $purchaseRequests = PurchaseRequest::where('status', 'received')->get();
        $poId = PurchaseOrder::generatePOId();
        return view('records.po.create', compact('departments', 'purchaseRequests', 'poId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'po_id' => 'required|unique:purchase_orders',
            'origin' => 'required',
            'subject' => 'required',
            'forward_to' => 'required',
            'remarks' => 'nullable',
            'amount' => 'required|numeric|min:0',
            'supplier' => 'required',
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'po_id' => $validated['po_id'],
            'date' => Carbon::now()->toDateString(),
            'time' => Carbon::now()->toTimeString(),
            'origin' => $validated['origin'],
            'received_by' => Auth::user()->name,
            'subject' => $validated['subject'],
            'file_case_no' => $request->file_case_no ?? '',
            'forward_to' => $validated['forward_to'],
            'current_location' => Auth::user()->department,
            'status' => 'pending',
            'remarks' => $validated['remarks'],
            'amount' => $validated['amount'],
            'supplier' => $validated['supplier'],
            'purchase_request_id' => $validated['purchase_request_id'],
        ]);

        // Create initial route record
        PurchaseOrderRoute::create([
            'purchase_order_id' => $purchaseOrder->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['forward_to'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'action_by' => Auth::user()->name,
        ]);

        return redirect()->route('records.po.index')
            ->with('success', 'Purchase Order created successfully.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $routes = $purchaseOrder->routes()->with(['fromDepartment', 'toDepartment'])->get();
        return view('records.po.show', compact('purchaseOrder', 'routes'));
    }

    public function route(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'to_department' => 'required',
            'notes' => 'nullable',
            'action_taken' => 'nullable',
        ]);

        // Create new route record
        PurchaseOrderRoute::create([
            'purchase_order_id' => $purchaseOrder->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['to_department'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'notes' => $validated['notes'],
            'action_taken' => $validated['action_taken'],
            'action_by' => Auth::user()->name,
        ]);

        // Update PO status and location
        $purchaseOrder->update([
            'current_location' => $validated['to_department'],
            'status' => 'pending',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Purchase Order routed successfully.');
    }

    public function receive(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Update the latest route record
        $latestRoute = $purchaseOrder->latestRoute();
        if ($latestRoute) {
            $latestRoute->update([
                'received_at' => Carbon::now(),
                'status' => 'received',
            ]);
        }

        // Update PO status
        $purchaseOrder->update([
            'status' => 'received',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Purchase Order received successfully.');
    }
} 