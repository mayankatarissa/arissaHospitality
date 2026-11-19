<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'phone', 'subject', 'message', 'status', 'resolved_at'])]
class ContactQuery extends Model
{
    protected function casts(): array
    {
        return ['resolved_at' => 'datetime'];
    }
}
