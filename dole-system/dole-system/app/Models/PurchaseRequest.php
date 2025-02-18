<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_no',
        'pr_date',
        'amount',
        'purpose',
        'status',
        'date_processed'
    ];

    protected $casts = [
        'pr_date' => 'date',
        'date_processed' => 'datetime',
        'amount' => 'decimal:2'
    ];
} 