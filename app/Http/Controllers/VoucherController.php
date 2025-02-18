<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\VoucherRoute;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Apply filters
        if ($request->filled('voucher_id')) {
            $query->where('voucher_id', 'like', '%' . $request->voucher_id . '%');
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
        if ($request->filled('payee')) {
            $query->where('payee', 'like', '%' . $request->payee . '%');
        }

        $vouchers = $query->latest('date')->latest('time')->paginate(10);
        $departments = Department::all();

        return view('records.vouchers.index', compact('vouchers', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        $voucherId = Voucher::generateVoucherId();
        return view('records.vouchers.create', compact('departments', 'voucherId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_id' => 'required|unique:vouchers',
            'origin' => 'required',
            'subject' => 'required',
            'forward_to' => 'required',
            'remarks' => 'nullable',
            'amount' => 'required|numeric|min:0',
            'payee' => 'required',
        ]);

        $voucher = Voucher::create([
            'voucher_id' => $validated['voucher_id'],
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
            'payee' => $validated['payee'],
        ]);

        // Create initial route record
        VoucherRoute::create([
            'voucher_id' => $voucher->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['forward_to'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'action_by' => Auth::user()->name,
        ]);

        return redirect()->route('records.vouchers.index')
            ->with('success', 'Voucher created successfully.');
    }

    public function show(Voucher $voucher)
    {
        $routes = $voucher->routes()->with(['fromDepartment', 'toDepartment'])->get();
        return view('records.vouchers.show', compact('voucher', 'routes'));
    }

    public function route(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'to_department' => 'required',
            'notes' => 'nullable',
            'action_taken' => 'nullable',
        ]);

        // Create new route record
        VoucherRoute::create([
            'voucher_id' => $voucher->id,
            'from_department' => Auth::user()->department,
            'to_department' => $validated['to_department'],
            'routed_at' => Carbon::now(),
            'status' => 'pending',
            'notes' => $validated['notes'],
            'action_taken' => $validated['action_taken'],
            'action_by' => Auth::user()->name,
        ]);

        // Update voucher status and location
        $voucher->update([
            'current_location' => $validated['to_department'],
            'status' => 'pending',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Voucher routed successfully.');
    }

    public function receive(Request $request, Voucher $voucher)
    {
        // Update the latest route record
        $latestRoute = $voucher->latestRoute();
        if ($latestRoute) {
            $latestRoute->update([
                'received_at' => Carbon::now(),
                'status' => 'received',
            ]);
        }

        // Update voucher status
        $voucher->update([
            'status' => 'received',
            'last_action_date' => Carbon::now()->toDateString(),
            'last_action_time' => Carbon::now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Voucher received successfully.');
    }
} 