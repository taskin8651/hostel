<?php

namespace App\Models\Hostel;

class Complaint extends BaseHostelModel
{
    protected $table = 'hostel_complaints';

    protected $casts = [
        'complaint_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [

        'title',

        'category',

        'user_id',

        'student_id',

        'room_id',

        'complaint_date',

        'status',

        'resolution_remark',
    ];

}
