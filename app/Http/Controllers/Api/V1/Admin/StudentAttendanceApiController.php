<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\StudentAttendance;
use App\Models\Hostel\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentAttendanceApiController extends Controller
{
    /**
     * CREATE ATTENDANCE
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([

            'student_id' => 'required|exists:hostel_students,id',

            'movement_type' => 'required|in:in,out',

            'remark' => 'nullable|string',

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $student = Student::find($request->student_id);

        if (!$student) {

            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Upload Image
        $imagePath = null;

        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')
                ->store('hostel/attendance', 'public');
        }

        // Current Time
        $now = now();

        // Create Attendance
        $attendance = StudentAttendance::create([

            'student_id' => $student->id,

            'attendance_date' => $now->format('Y-m-d'),

            'attendance_datetime' => $now,

            'movement_type' => $request->movement_type,

            'status' => $request->movement_type == 'in'
                ? 'present'
                : null,

            'punch_in_time' => $request->movement_type == 'in'
                ? $now
                : null,

            'punch_out_time' => $request->movement_type == 'out'
                ? $now
                : null,

            'remark' => $request->remark,

            'image' => $imagePath,
        ]);

        return response()->json([

            'status' => true,

            'message' => ucfirst($request->movement_type) . ' attendance marked successfully',

            'data' => [

                'attendance_id' => $attendance->id,

                'student_id' => $student->id,

                'student_name' => $student->name,

                'movement_type' => ucfirst($attendance->movement_type),

                'status' => ucfirst($attendance->status ?? 'out'),

                'attendance_date' => Carbon::parse($attendance->attendance_date)
                    ->format('d M Y'),

                'attendance_time' => Carbon::parse($attendance->attendance_datetime)
                    ->format('h:i A'),

                'punch_in_time' => $attendance->punch_in_time
                    ? Carbon::parse($attendance->punch_in_time)->format('h:i A')
                    : null,

                'punch_out_time' => $attendance->punch_out_time
                    ? Carbon::parse($attendance->punch_out_time)->format('h:i A')
                    : null,

                'remark' => $attendance->remark,

                'image' => $attendance->image
                    ? asset('storage/' . $attendance->image)
                    : null,
            ]
        ]);
    }
}
