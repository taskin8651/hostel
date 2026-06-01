<?php

namespace App\Models\Hostel;

class HostelExpense extends BaseHostelModel
{
    protected $table = 'hostel_hostel_expenses';

    protected $casts = [
        'expense_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
