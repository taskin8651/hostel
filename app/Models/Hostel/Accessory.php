<?php

namespace App\Models\Hostel;

class Accessory extends BaseHostelModel
{
    protected $table = 'hostel_accessories';

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
