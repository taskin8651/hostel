<?php

namespace App\Models\Hostel;

class Income extends BaseHostelModel
{
    protected $table = 'hostel_incomes';

    protected $casts = [
        'income_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
