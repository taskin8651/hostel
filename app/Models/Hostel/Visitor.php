<?php

namespace App\Models\Hostel;

class Visitor extends BaseHostelModel
{
    protected $table = 'hostel_visitors';

    protected $casts = [
        'visit_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [

        'name',

        'mobile',

        'relation',

        'purpose',

        'student_id',

        'room_id',

        'visit_date',

        'in_time',

        'out_time',

        'id_proof',
    ];


    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
