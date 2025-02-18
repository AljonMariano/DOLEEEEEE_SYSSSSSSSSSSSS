<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RouteSlip extends Model
{
    protected $fillable = [
        'slip_id',
        'date',
        'time',
        'origin',
        'received_by',
        'subject',
        'file_case_no',
        'forward_to',
        'current_location',
        'status',
        'remarks',
        'document_type',
        'reference_id',
        'completely_signed',
        'last_action_date',
        'last_action_time',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'completely_signed' => 'boolean',
        'last_action_date' => 'date',
        'last_action_time' => 'datetime',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(RouteSlipRoute::class);
    }

    public function latestRoute()
    {
        return $this->routes()->latest('routed_at')->first();
    }

    public function referencedDocument()
    {
        return match($this->document_type) {
            'PO' => PurchaseOrder::where('po_id', $this->reference_id)->first(),
            'PR' => PurchaseRequest::where('pr_id', $this->reference_id)->first(),
            'Voucher' => Voucher::where('voucher_id', $this->reference_id)->first(),
            'Order' => Order::where('order_id', $this->reference_id)->first(),
            default => null,
        };
    }

    public static function generateSlipId()
    {
        $year = date('Y');
        $month = date('m');
        $lastSlip = self::where('slip_id', 'like', "{$year}{$month}%")
            ->orderBy('slip_id', 'desc')
            ->first();

        if (!$lastSlip) {
            return $year . $month . '001';
        }

        $lastNumber = intval(substr($lastSlip->slip_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return $year . $month . $newNumber;
    }
} 