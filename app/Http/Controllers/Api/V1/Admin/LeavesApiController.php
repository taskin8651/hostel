<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LeavesApiController extends Controller
{
    /**
     * ALL LEAVES
     */
    public function index()
    {
        $leaves = Leave::with(['student', 'staff'])
            ->latest()
            ->get()
            ->map(function ($leave) {

                return $this->formatLeave($leave);
            });

        return response()->json([

            'status' => true,

            'message' => 'Leaves fetched successfully',

            'data' => $leaves
        ]);
    }

    /**
     * APPLY LEAVE
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required|exists:users,id',

            'leave_type' => 'required|string|max:100',

            'from_date' => 'required|date',

            'to_date' => 'required|date|after_or_equal:from_date',

            'reason' => 'required|string|max:1000',

        ], [

            'user_id.required' => 'User required',

            'user_id.exists' => 'User not found',

            'leave_type.required' => 'Leave type required',

            'from_date.required' => 'From date required',

            'to_date.required' => 'To date required',

            'reason.required' => 'Reason required',
        ]);

        // VALIDATION ERROR
        if ($validator->fails()) {

            return response()->json([

                'status' => false,

                'message' => $validator->errors()->first()

            ], 422);
        }

        // USER
        $user = User::with([
            'roles',
            'studentProfile',
            'staffProfile'
        ])->find($request->user_id);

        if (!$user) {

            return response()->json([

                'status' => false,

                'message' => 'User not found'

            ], 404);
        }

        // DEFAULT VALUES
        $personType = null;

        $studentId = null;

        $staffId = null;

        // AUTO DETECT STUDENT
        if ($user->studentProfile) {

            $personType = 'student';

            $studentId = $user->studentProfile->id;
        }

        // AUTO DETECT STAFF
        elseif ($user->staffProfile) {

            $personType = 'staff';

            $staffId = $user->staffProfile->id;
        }

        // ADMIN
        else {

            $personType = 'admin';
        }

        // CREATE LEAVE
        Leave::create([

            'person_type' => $personType,

            'user_id' => $user->id,

            'student_id' => $studentId,

            'staff_id' => $staffId,

            'leave_type' => $request->leave_type,

            'from_date' => $request->from_date,

            'to_date' => $request->to_date,

            'reason' => $request->reason,

            'status' => 'pending',
        ]);

        return response()->json([

            'status' => true,

            'message' => 'Leave applied successfully'
        ]);
    }

    /**
     * SINGLE LEAVE
     */
    public function show($id)
    {
        $leave = Leave::with([
            'student',
            'staff'
        ])->find($id);

        if (!$leave) {

            return response()->json([

                'status' => false,

                'message' => 'Leave not found'

            ], 404);
        }

        return response()->json([

            'status' => true,

            'message' => 'Leave fetched successfully',

            'data' => $this->formatLeave($leave)
        ]);
    }

    /**
     * FORMAT LEAVE
     */
    private function formatLeave($leave)
    {
        return [

            'id' => $leave->id,

            'person_type' => ucfirst($leave->person_type),

            'leave_type' => $leave->leave_type,

            'from_date' => $leave->from_date
                ? Carbon::parse($leave->from_date)->format('d M Y')
                : null,

            'to_date' => $leave->to_date
                ? Carbon::parse($leave->to_date)->format('d M Y')
                : null,

            'total_days' => Carbon::parse($leave->from_date)
                ->diffInDays(Carbon::parse($leave->to_date)) + 1,

            'reason' => $leave->reason,

            'status' => ucfirst($leave->status),

            // USER
            'user' => [

                'id' => $leave->user_id,
            ],

            // STUDENT
            'student' => $leave->student ? [

                'id' => $leave->student->id,

                'name' => $leave->student->name,

                'mobile' => $leave->student->mobile,

            ] : null,

            // STAFF
            'staff' => $leave->staff ? [

                'id' => $leave->staff->id,

                'name' => $leave->staff->name,

                'mobile' => $leave->staff->mobile,

            ] : null,
        ];
    }
}