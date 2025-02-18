<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderRoute;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // Apply filters
        if ($request->filled('order_id')) {
            $query->where('order_id', 'like', '%' . $request->order_id . '%');
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

        $orders = $query->latest('date')->latest('time')->paginate(10);
        $departments = Department::all();

        return view('records.orders.index', compact('orders', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $orderId = Order::generateOrderId();
        return view('records.orders.create', compact('departments', 'orderId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|unique:orders',
            'origin' => 'required',
            'subject' => 'required',
            'forward_to' => 'required',
            'remarks' => 'nullable',
        ]);

        $order = Order::create([
            'order_id' => $validated['order_id'],
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
        ]);

        // Create initial route record
        OrderRoute::create([
            'order_id' => $order->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['forward_to'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'action_by' => Auth::user()->name,
        ]);

        return redirect()->route('records.orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $routes = $order->routes()->with(['fromDepartment', 'toDepartment'])->get();
        return view('records.orders.show', compact('order', 'routes'));
    }

    public function route(Request $request, Order $order)
    {
        $validated = $request->validate([
            'to_department' => 'required',
            'notes' => 'nullable',
            'action_taken' => 'nullable',
        ]);

        // Create new route record
        OrderRoute::create([
            'order_id' => $order->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['to_department'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'notes' => $validated['notes'],
            'action_taken' => $validated['action_taken'],
            'action_by' => Auth::user()->name,
        ]);

        // Update order status and location
        $order->update([
            'current_location' => $validated['to_department'],
            'status' => 'pending',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Order routed successfully.');
    }

    public function receive(Request $request, Order $order)
    {
        // Update the latest route record
        $latestRoute = $order->latestRoute();
        if ($latestRoute) {
            $latestRoute->update([
                'received_at' => Carbon::now(),
                'status' => 'received',
            ]);
        }

        // Update order status
        $order->update([
            'status' => 'received',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Order received successfully.');
    }
} 