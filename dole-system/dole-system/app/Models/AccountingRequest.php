<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingRequest extends Model
{
    protected $fillable = [
        'budget_request_id',
        'ors_no',
        'po_no',
        'po_date',
        'dv_no',
        'dv_date',
        'payee',
        'amount',
        'status',
        'date_processed',
        'remarks',
        'payment_type',
        'reference_no',
        'payment_date',
        'tax',
        'payment_remarks'
    ];

    protected $casts = [
        'po_date' => 'date',
        'dv_date' => 'date',
        'payment_date' => 'date',
        'date_processed' => 'datetime',
        'amount' => 'decimal:2',
        'tax' => 'decimal:2'
    ];

    public function budgetRequest()
    {
        return $this->belongsTo(BudgetRequest::class);
    }
} 