<?php

namespace App\Models\Hostel;

class Notice extends BaseHostelModel
{
    protected $table = 'hostel_notices';

    protected $casts = [
        'notice_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
