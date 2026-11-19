<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'event_type', 'event_date', 'guest_count', 'message', 'status', 'resolved_at'])]
class EventEnquiry extends Model
{
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'guest_count' => 'integer',
            'resolved_at' => 'datetime',
        ];
    }
}
