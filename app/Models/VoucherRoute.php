<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherRoute extends Model
{
    protected $fillable = [
        'voucher_id',
        'from_department',
        'to_department',
        'routed_at',
        'received_at',
        'status',
        'notes',
        'action_taken',
        'action_by',
    ];

    protected $casts = [
        'routed_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }
} 