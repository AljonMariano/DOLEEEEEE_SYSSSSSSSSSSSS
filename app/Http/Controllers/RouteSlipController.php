<?php

namespace App\Http\Controllers;

use App\Models\RouteSlip;
use App\Models\RouteSlipRoute;
use App\Models\Department;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Voucher;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RouteSlipController extends Controller
{
    public function index(Request $request)
    {
        $query = RouteSlip::query();

        // Apply filters
        if ($request->filled('slip_id')) {
            $query->where('slip_id', 'like', '%' . $request->slip_id . '%');
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
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        $routeSlips = $query->latest('date')->latest('time')->paginate(10);
        $departments = Department::all();

        return view('records.route-slips.index', compact('routeSlips', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $slipId = RouteSlip::generateSlipId();
        
        // Get available documents for reference
        $purchaseOrders = PurchaseOrder::where('status', 'received')->get();
        $purchaseRequests = PurchaseRequest::where('status', 'received')->get();
        $vouchers = Voucher::where('status', 'received')->get();
        $orders = Order::where('status', 'received')->get();

        return view('records.route-slips.create', compact(
            'departments', 
            'slipId',
            'purchaseOrders',
            'purchaseRequests',
            'vouchers',
            'orders'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slip_id' => 'required|unique:route_slips',
            'origin' => 'required',
            'subject' => 'required',
            'forward_to' => 'required',
            'remarks' => 'nullable',
            'document_type' => 'required|in:PO,PR,Voucher,Order',
            'reference_id' => 'required',
        ]);

        $routeSlip = RouteSlip::create([
            'slip_id' => $validated['slip_id'],
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
            'document_type' => $validated['document_type'],
            'reference_id' => $validated['reference_id'],
        ]);

        // Create initial route record
        RouteSlipRoute::create([
            'route_slip_id' => $routeSlip->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['forward_to'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'action_by' => Auth::user()->name,
        ]);

        return redirect()->route('records.route-slips.index')
            ->with('success', 'Route Slip created successfully.');
    }

    public function show(RouteSlip $routeSlip)
    {
        $routes = $routeSlip->routes()->with(['fromDepartment', 'toDepartment'])->get();
        $referencedDocument = $routeSlip->referencedDocument();
        return view('records.route-slips.show', compact('routeSlip', 'routes', 'referencedDocument'));
    }

    public function route(Request $request, RouteSlip $routeSlip)
    {
        $validated = $request->validate([
            'to_department' => 'required',
            'notes' => 'nullable',
            'action_taken' => 'nullable',
        ]);

        // Create new route record
        RouteSlipRoute::create([
            'route_slip_id' => $routeSlip->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['to_department'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'notes' => $validated['notes'],
            'action_taken' => $validated['action_taken'],
            'action_by' => Auth::user()->name,
        ]);

        // Update route slip status and location
        $routeSlip->update([
            'current_location' => $validated['to_department'],
            'status' => 'pending',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Route Slip routed successfully.');
    }

    public function receive(Request $request, RouteSlip $routeSlip)
    {
        // Update the latest route record
        $latestRoute = $routeSlip->latestRoute();
        if ($latestRoute) {
            $latestRoute->update([
                'received_at' => Carbon::now(),
                'status' => 'received',
            ]);
        }

        // Update route slip status
        $routeSlip->update([
            'status' => 'received',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Route Slip received successfully.');
    }
} 