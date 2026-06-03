<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ComplaintsApiController extends Controller
{
    /**
     * ALL COMPLAINTS
     */
    public function index()
    {
        $complaints = Complaint::latest()
            ->get()
            ->map(function ($complaint) {

                return $this->formatComplaint($complaint);
            });

        return response()->json([

            'status' => true,

            'message' => 'Complaints fetched successfully',

            'data' => $complaints
        ]);
    }

    /**
     * CREATE COMPLAINT
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'user_id' => 'required|exists:users,id',

            'title' => 'required|string|max:255',

            'category' => 'required|string|max:100',

        ], [

            'user_id.required' => 'User required',

            'user_id.exists' => 'User not found',

            'title.required' => 'Title required',

            'category.required' => 'Category required',
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
            'studentProfile.room',
            'staffProfile'
        ])->find($request->user_id);

        if (!$user) {

            return response()->json([

                'status' => false,

                'message' => 'User not found'

            ], 404);
        }

        // DEFAULT VALUES
        $studentId = null;

        $roomId = null;

        // STUDENT
        if ($user->studentProfile) {

            $studentId = $user->studentProfile->id;

            $roomId = $user->studentProfile->room_id;
        }

        // CREATE COMPLAINT
        Complaint::create([

            'title' => $request->title,

            'category' => strtolower($request->category),

            'user_id' => $user->id,

            'student_id' => $studentId,

            'room_id' => $roomId,

            'complaint_date' => now(),

            'status' => 'pending',

            'resolution_remark' => null,
        ]);

        return response()->json([

            'status' => true,

            'message' => 'Complaint submitted successfully'
        ]);
    }

    /**
     * SINGLE COMPLAINT
     */
    public function show($id)
    {
        $complaint = Complaint::find($id);

        if (!$complaint) {

            return response()->json([

                'status' => false,

                'message' => 'Complaint not found'

            ], 404);
        }

        return response()->json([

            'status' => true,

            'message' => 'Complaint fetched successfully',

            'data' => $this->formatComplaint($complaint)
        ]);
    }

    /**
     * FORMAT COMPLAINT
     */
    private function formatComplaint($complaint)
    {
        return [

            'id' => $complaint->id,

            'title' => $complaint->title,

            'category' => ucfirst($complaint->category),

            'complaint_date' => $complaint->complaint_date
                ? Carbon::parse($complaint->complaint_date)->format('d M Y')
                : null,

            'status' => ucfirst($complaint->status),

            'resolution_remark' => $complaint->resolution_remark,
        ];
    }
}