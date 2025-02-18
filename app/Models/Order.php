<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_id',
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
        return $this->hasMany(OrderRoute::class);
    }

    public function latestRoute()
    {
        return $this->routes()->latest('routed_at')->first();
    }

    public static function generateOrderId()
    {
        $year = date('Y');
        $month = date('m');
        $lastOrder = self::where('order_id', 'like', "{$year}{$month}%")
            ->orderBy('order_id', 'desc')
            ->first();

        if (!$lastOrder) {
            return $year . $month . '001';
        }

        $lastNumber = intval(substr($lastOrder->order_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        return $year . $month . $newNumber;
    }
} 