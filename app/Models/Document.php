<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Document extends Model
{
    protected $fillable = [
        'doc_id',
        'date_received',
        'time_received',
        'origin',
        'subject',
        'forward_to',
        'status',
        'remarks'
    ];

    protected $casts = [
        'date_received' => 'date',
        'time_received' => 'datetime',
    ];

    // Relationship with document routes
    public function routes()
    {
        return $this->hasMany(DocumentRoute::class);
    }

    // Get current location/department of the document
    public function currentLocation()
    {
        return $this->routes()
            ->orderBy('routed_at', 'desc')
            ->first()?->to_department ?? $this->forward_to;
    }

    // Get full routing history
    public function getRoutingHistory()
    {
        return $this->routes()->orderBy('routed_at', 'asc')->get();
    }

    // Generate next document ID
    public static function generateDocId()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = $year . $month;
        
        $lastDoc = self::where('doc_id', 'like', $prefix . '%')
            ->orderBy('doc_id', 'desc')
            ->first();

        if (!$lastDoc) {
            return $prefix . '001';
        }

        $lastNumber = intval(substr($lastDoc->doc_id, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $newNumber;
    }

    // Route document to another department
    public function routeTo($toDepartment, $notes = null)
    {
        $currentLocation = $this->currentLocation();

        return $this->routes()->create([
            'from_department' => $currentLocation,
            'to_department' => $toDepartment,
            'routed_at' => now(),
            'status' => 'pending',
            'notes' => $notes
        ]);
    }

    // Mark document as received by department
    public function markAsReceived($department, $notes = null)
    {
        $route = $this->routes()
            ->where('to_department', $department)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($route) {
            $route->update([
                'received_at' => now(),
                'status' => 'received',
                'notes' => $notes
            ]);
        }

        return $route;
    }
} 