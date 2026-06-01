<?php

namespace App\Models\Hostel;

class Event extends BaseHostelModel
{
    protected $table = 'hostel_events';

    protected $casts = [
        'event_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
