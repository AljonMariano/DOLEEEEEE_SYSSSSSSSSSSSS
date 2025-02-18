<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description'
    ];

    // Get all documents currently routed to this department
    public function routedDocuments()
    {
        return $this->hasMany(DocumentRoute::class, 'to_department', 'code')
            ->where('status', 'pending');
    }

    // Get all documents that originated from this department
    public function originatedDocuments()
    {
        return $this->hasMany(DocumentRoute::class, 'from_department', 'code');
    }
} 