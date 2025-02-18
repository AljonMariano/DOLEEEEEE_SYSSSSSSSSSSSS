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
        'remarks',
        'completely_signed',
        'current_location',
        'last_action_date',
        'last_action_time'
    ];

    protected $casts = [
        'date_received' => 'date',
        'time_received' => 'datetime',
        'last_action_date' => 'date',
        'last_action_time' => 'datetime',
        'completely_signed' => 'boolean'
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

    // Generate next document ID (YYYYMMXXX format)
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
        
        $route = $this->routes()->create([
            'from_department' => $currentLocation,
            'to_department' => $toDepartment,
            'routed_at' => now(),
            'status' => 'pending',
            'notes' => $notes
        ]);

        // Update document's current location and last action
        $this->update([
            'current_location' => $toDepartment,
            'last_action_date' => now(),
            'last_action_time' => now()
        ]);

        return $route;
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

            // Update document's last action
            $this->update([
                'last_action_date' => now(),
                'last_action_time' => now()
            ]);
        }

        return $route;
    }

    // Mark document as completely signed
    public function markAsCompleted()
    {
        $this->update([
            'completely_signed' => true,
            'status' => 'completed',
            'last_action_date' => now(),
            'last_action_time' => now()
        ]);
    }

    // Get time elapsed since last action
    public function getTimeElapsedAttribute()
    {
        if ($this->last_action_date) {
            try {
                return Carbon::parse($this->last_action_date)
                    ->setTimeFromTimeString($this->last_action_time ?? '00:00:00')
                    ->diffForHumans();
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    // Get total processing time
    public function getTotalProcessingTimeAttribute()
    {
        $firstRoute = $this->routes()->oldest()->first();
        $lastRoute = $this->routes()->latest()->first();

        if ($firstRoute && $lastRoute) {
            return Carbon::parse($firstRoute->routed_at)
                ->diffForHumans(Carbon::parse($lastRoute->received_at ?? now()), true);
        }
        return null;
    }

    // Scope for filtering by date range
    public function scopeDateRange($query, $from, $to)
    {
        if ($from) {
            $query->whereDate('date_received', '>=', $from);
        }
        if ($to) {
            $query->whereDate('date_received', '<=', $to);
        }
    }

    // Scope for filtering by department
    public function scopeInDepartment($query, $department)
    {
        return $query->where('current_location', $department)
            ->orWhereHas('routes', function ($q) use ($department) {
                $q->where('to_department', $department);
            });
    }

    // Format time received
    public function getFormattedTimeReceivedAttribute()
    {
        return $this->time_received ? Carbon::parse($this->time_received)->format('h:i A') : null;
    }

    // Format last action time
    public function getFormattedLastActionTimeAttribute()
    {
        return $this->last_action_time ? Carbon::parse($this->last_action_time)->format('h:i A') : null;
    }
}
