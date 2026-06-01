<?php

namespace App\Models\Hostel;

class Expense extends BaseHostelModel
{
    protected $table = 'hostel_expenses';

    protected $casts = [
        'expense_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
