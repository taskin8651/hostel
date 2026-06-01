<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController
{
    public function index()
    {
        $modules = config('hostel.modules');
        $stats = [];

        foreach ($modules as $key => $module) {
            $stats[$key] = Schema::hasTable($module['table'])
                ? DB::table($module['table'])->whereNull('deleted_at')->count()
                : 0;
        }

        $pendingFees = Schema::hasTable('hostel_fee_payments')
            ? DB::table('hostel_fee_payments')->whereNull('deleted_at')->sum('due_amount')
            : 0;

        $availableBeds = Schema::hasTable('hostel_beds')
            ? DB::table('hostel_beds')->whereNull('deleted_at')->where('status', 'vacant')->count()
            : 0;

        $occupiedRooms = Schema::hasTable('hostel_rooms')
            ? DB::table('hostel_rooms')->whereNull('deleted_at')->where('status', 'occupied')->count()
            : 0;

        $activeComplaints = Schema::hasTable('hostel_complaints')
            ? DB::table('hostel_complaints')->whereNull('deleted_at')->whereIn('status', ['pending', 'in_progress'])->count()
            : 0;

        $todayAttendance = Schema::hasTable('hostel_student_attendance')
            ? DB::table('hostel_student_attendance')->whereNull('deleted_at')->whereDate('attendance_date', now()->toDateString())->count()
            : 0;

        $monthlyExpense = Schema::hasTable('hostel_expenses')
            ? DB::table('hostel_expenses')->whereNull('deleted_at')->whereMonth('expense_date', now()->month)->sum('amount')
            : 0;

        $monthlyIncome = Schema::hasTable('hostel_incomes')
            ? DB::table('hostel_incomes')->whereNull('deleted_at')->whereMonth('income_date', now()->month)->sum('amount')
            : 0;

        $upcomingEvents = Schema::hasTable('hostel_events')
            ? DB::table('hostel_events')->whereNull('deleted_at')->where('status', 'published')->whereDate('event_date', '>=', now()->toDateString())->orderBy('event_date')->limit(5)->get()
            : collect();

        $recentNotices = Schema::hasTable('hostel_notices')
            ? DB::table('hostel_notices')->whereNull('deleted_at')->where('status', 'published')->latest('notice_date')->limit(5)->get()
            : collect();

        return view('home', compact(
            'modules',
            'stats',
            'pendingFees',
            'availableBeds',
            'occupiedRooms',
            'activeComplaints',
            'todayAttendance',
            'monthlyExpense',
            'monthlyIncome',
            'upcomingEvents',
            'recentNotices'
        ));
    }
}
