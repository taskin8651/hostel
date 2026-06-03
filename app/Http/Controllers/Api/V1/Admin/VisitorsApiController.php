<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Visitor;
use App\Models\Hostel\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VisitorsApiController extends Controller
{
    /**
     * ALL VISITORS
     */
    public function index()
    {
        $visitors = Visitor::with(['student', 'room'])
            ->latest()
            ->get()
            ->map(function ($visitor) {

                return $this->formatVisitor($visitor);
            });

        return response()->json([

            'status' => true,

            'message' => 'Visitors fetched successfully',

            'data' => $visitors
        ]);
    }

    /**
     * CREATE VISITOR
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string|max:255',

            'mobile' => 'required|string|max:20',

            'relation' => 'required|string|max:100',

            'purpose' => 'required|string|max:255',

            'student_id' => 'required|exists:hostel_students,id',

            'visit_date' => 'required|date',

            'in_time' => 'required',

            'id_proof' => 'nullable|string|max:255',

        ], [

            'name.required' => 'Visitor name required',

            'mobile.required' => 'Mobile number required',

            'relation.required' => 'Relation required',

            'purpose.required' => 'Purpose required',

            'student_id.required' => 'Student required',

            'student_id.exists' => 'Student not found',

            'visit_date.required' => 'Visit date required',

            'in_time.required' => 'In time required',
        ]);

        // VALIDATION ERROR
        if ($validator->fails()) {

            return response()->json([

                'status' => false,

                'message' => $validator->errors()->first()

            ], 422);
        }

        $student = Student::find($request->student_id);

        // STUDENT NOT FOUND
        if (!$student) {

            return response()->json([

                'status' => false,

                'message' => 'Student not found'

            ], 404);
        }

        // CREATE VISITOR
        Visitor::create([

            'name' => $request->name,

            'mobile' => $request->mobile,

            'relation' => $request->relation,

            'purpose' => $request->purpose,

            'student_id' => $request->student_id,

            // AUTO ROOM ID
            'room_id' => $student->room_id,

            'visit_date' => $request->visit_date,

            'in_time' => $request->in_time,

            // DEFAULT NULL
            'out_time' => null,

            'id_proof' => $request->id_proof,
        ]);

        // SUCCESS RESPONSE
        return response()->json([

            'status' => true,

            'message' => 'Visitor added successfully'
        ]);
    }

    /**
     * SINGLE VISITOR
     */
    public function show($id)
    {
        $visitor = Visitor::with(['student', 'room'])->find($id);

        if (!$visitor) {

            return response()->json([

                'status' => false,

                'message' => 'Visitor not found'

            ], 404);
        }

        return response()->json([

            'status' => true,

            'message' => 'Visitor fetched successfully',

            'data' => $this->formatVisitor($visitor)
        ]);
    }

    /**
     * FORMAT VISITOR
     */
    private function formatVisitor($visitor)
    {
        return [

            'id' => $visitor->id,

            'name' => $visitor->name,

            'mobile' => $visitor->mobile,

            'relation' => $visitor->relation,

            'purpose' => $visitor->purpose,

            'visit_date' => $visitor->visit_date
                ? Carbon::parse($visitor->visit_date)->format('d M Y')
                : null,

            'day' => $visitor->visit_date
                ? Carbon::parse($visitor->visit_date)->format('l')
                : null,

            'in_time' => $visitor->in_time
                ? Carbon::parse($visitor->in_time)->format('h:i A')
                : null,

            'id_proof' => $visitor->id_proof,

            // STUDENT
            'student' => [

                'id' => optional($visitor->student)->id,

                'name' => optional($visitor->student)->name,

                'mobile' => optional($visitor->student)->mobile,
            ],

            // ROOM
            'room' => [

                'room_number' => optional($visitor->room)->room_number,

                'floor_number' => optional($visitor->room)->floor_number,
            ],
        ];
    }
}