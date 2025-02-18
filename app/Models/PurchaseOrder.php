<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_id',
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
        'amount',
        'supplier',
        'purchase_request_id',
        'completely_signed',
        'last_action_date',
        'last_action_time',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'amount' => 'decimal:2',
        'completely_signed' => 'boolean',
        'last_action_date' => 'date',
        'last_action_time' => 'datetime',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(PurchaseOrderRoute::class);
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function latestRoute()
    {
        return $this->routes()->latest('routed_at')->first();
    }

    public static function generatePOId()
    {
        $year = date('Y');
        $month = date('m');
        $lastPO = self::where('po_id', 'like', "{$year}{$month}%")
            ->orderBy('po_id', 'desc')
            ->first();

        if (!$lastPO) {
            return $year . $month . '001';
        }

        $lastNumber = intval(substr($lastPO->po_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return $year . $month . $newNumber;
    }
} 