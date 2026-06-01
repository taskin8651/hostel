<?php

namespace App\Models\Hostel;

class Bed extends BaseHostelModel
{
    protected $table = 'hostel_beds';

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class, 'bed_id');
    }
}
