<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftDeleteHistory extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'parent_type',
        'parent_id',
        'action',
        'deletion_source',
        'event_at',
    ];

    protected $casts = [
        'event_at' => 'datetime',
    ];
}