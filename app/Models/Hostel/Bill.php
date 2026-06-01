<?php

namespace App\Models\Hostel;

class Bill extends BaseHostelModel
{
    protected $table = 'hostel_bills';

    protected $casts = [
        'bill_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
