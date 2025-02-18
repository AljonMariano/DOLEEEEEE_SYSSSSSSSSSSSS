<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRoute extends Model
{
    protected $fillable = [
        'document_id',
        'from_department',
        'to_department',
        'routed_at',
        'received_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'routed_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    // Relationship with document
    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    // Get time taken for this routing step
    public function getTimeTakenAttribute()
    {
        if (!$this->received_at) {
            return null;
        }

        return $this->routed_at->diffForHumans($this->received_at, true);
    }

    // Check if this route is pending
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Check if this route is completed
    public function isCompleted()
    {
        return $this->status === 'received';
    }
} 