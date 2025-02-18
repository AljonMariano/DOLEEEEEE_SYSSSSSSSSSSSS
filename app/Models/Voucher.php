<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'voucher_id',
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
        'payee',
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
        return $this->hasMany(VoucherRoute::class);
    }

    public function latestRoute()
    {
        return $this->routes()->latest('routed_at')->first();
    }

    public static function generateVoucherId()
    {
        $year = date('Y');
        $month = date('m');
        $lastVoucher = self::where('voucher_id', 'like', "{$year}{$month}%")
            ->orderBy('voucher_id', 'desc')
            ->first();

        if (!$lastVoucher) {
            return $year . $month . '001';
        }

        $lastNumber = intval(substr($lastVoucher->voucher_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return $year . $month . $newNumber;
    }
} 