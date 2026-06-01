<?php

namespace App\Models\Hostel;

class FoodMenu extends BaseHostelModel
{
    protected $table = 'hostel_food_menus';

    protected $casts = [
        'menu_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
