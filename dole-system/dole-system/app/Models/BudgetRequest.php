<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BudgetRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'pr_no',
        'pr_date',
        'ors_no',
        'ors_date',
        'payee',
        'purpose',
        'amount',
        'status',
        'date_processed'
    ];

    protected $casts = [
        'pr_date' => 'date',
        'ors_date' => 'date',
        'date_processed' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function accountingRequest()
    {
        return $this->hasOne(AccountingRequest::class);
    }
} 