<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseRequest extends Model
{
    protected $fillable = [
        'pr_id',
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
        'estimated_amount',
        'requesting_department',
        'completely_signed',
        'last_action_date',
        'last_action_time',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'estimated_amount' => 'decimal:2',
        'completely_signed' => 'boolean',
        'last_action_date' => 'date',
        'last_action_time' => 'datetime',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(PurchaseRequestRoute::class);
    }

    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    public function latestRoute()
    {
        return $this->routes()->latest('routed_at')->first();
    }

    public static function generatePRId()
    {
        $year = date('Y');
        $month = date('m');
        $lastPR = self::where('pr_id', 'like', "{$year}{$month}%")
            ->orderBy('pr_id', 'desc')
            ->first();

        if (!$lastPR) {
            return $year . $month . '001';
        }

        $lastNumber = intval(substr($lastPR->pr_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return $year . $month . $newNumber;
    }
} 