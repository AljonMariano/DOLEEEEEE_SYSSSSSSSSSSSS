<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = [
        'date',
        'doc_id',
        'origin_source',
        'time',
        'subject',
        'forward_to',
        'tssd',
        'imsd',
        'planning_officer',
        'hrmo',
        'supply',
        'acctg',
        'malsu',
        'oard',
        'ord',
        'is_completely_signed',
        'remarks',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'tssd' => 'boolean',
        'imsd' => 'boolean',
        'planning_officer' => 'boolean',
        'hrmo' => 'boolean',
        'supply' => 'boolean',
        'acctg' => 'boolean',
        'malsu' => 'boolean',
        'oard' => 'boolean',
        'ord' => 'boolean',
        'is_completely_signed' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 