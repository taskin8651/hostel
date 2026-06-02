<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\FoodMenu;
use Carbon\Carbon;

class FoodMenusApiController extends Controller
{
    /**
     * FULL WEEK MENU
     */
    public function index()
    {
        $menus = FoodMenu::orderBy('id')
            ->get()
            ->map(function ($menu) {

                return $this->formatMenu($menu);
            });

        return response()->json([

            'status' => true,
            'message' => 'Food menus fetched successfully',

            'data' => $menus
        ]);
    }

    /**
     * TODAY MENU
     */
    public function todayMenu()
    {
        $today = strtolower(Carbon::now()->format('l'));

        $menu = FoodMenu::where('day', $today)->first();

        if (!$menu) {

            return response()->json([
                'status' => false,
                'message' => 'Today food menu not found'
            ], 404);
        }

        return response()->json([

            'status' => true,
            'message' => 'Today menu fetched successfully',

            'data' => $this->formatMenu($menu)
        ]);
    }

    /**
     * DAY WISE MENU
     * Example: monday
     */
    public function dayMenu($day)
    {
        $menu = FoodMenu::where('day', strtolower($day))->first();

        if (!$menu) {

            return response()->json([
                'status' => false,
                'message' => 'Food menu not found'
            ], 404);
        }

        return response()->json([

            'status' => true,
            'message' => ucfirst($day) . ' menu fetched successfully',

            'data' => $this->formatMenu($menu)
        ]);
    }

    /**
     * FORMAT MENU
     */
    private function formatMenu($menu)
    {
        return [

            'id' => $menu->id,

            'day' => ucfirst($menu->day),

            'morning_snacks' => $menu->morning_snacks,

            'breakfast' => $menu->breakfast,

            'lunch' => $menu->lunch,

            'evening_snacks' => $menu->evening_snacks,

            'dinner' => $menu->dinner,
        ];
    }
}