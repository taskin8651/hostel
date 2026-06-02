<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class AuthApiController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with([
            'roles',
            'studentProfile.branch',
            'studentProfile.room',
            'studentProfile.activeAllocation',
            'studentProfile.accessories',
        ])->where('email', $request->email)->first();

        // Invalid Login
        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Only Student Login
        $role = optional($user->roles->first())->title;

        if ($role !== 'Student') {

            return response()->json([
                'status' => false,
                'message' => 'Only student login allowed in mobile app'
            ], 403);
        }

        $student = $user->studentProfile;

        if (!$student) {

            return response()->json([
                'status' => false,
                'message' => 'Student profile not found'
            ], 404);
        }

        // Remove old tokens
        $user->tokens()->delete();

        // Create Token
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => $this->studentResponse($user, $student, $token)
        ]);
    }

    /**
     * PROFILE
     */
    public function profile($user_id, Request $request)
    {
        $user = User::with([
            'roles',
            'studentProfile.branch',
            'studentProfile.room',
            'studentProfile.activeAllocation',
            'studentProfile.accessories',
        ])->find($user_id);

        // User Not Found
        if (!$user) {

            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Token User Check
        if ($request->user()->id != $user->id) {

            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $student = $user->studentProfile;

        if (!$student) {

            return response()->json([
                'status' => false,
                'message' => 'Student profile not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Profile fetched successfully',
            'data' => $this->studentResponse($user, $student)
        ]);
    }


    /**
     * LOGOUT
     */
    public function logout($user_id, Request $request)
    {
        $user = User::find($user_id);

        // User Not Found
        if (!$user) {

            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Token User Check
        if ($request->user()->id != $user->id) {

            return response()->json([
                'status' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Delete Current Token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ]);
    }


    /**
     * COMMON STUDENT RESPONSE
     */
    private function studentResponse($user, $student, $token = null)
    {
        return [

            // TOKEN
            'token' => $token,

            // USER
            'user_id' => $user->id,
            'role' => optional($user->roles->first())->title,

            // STUDENT
            'student_id' => $student->id,
            'registration_no' => $student->registration_no,

            'name' => $student->name,
            'email' => $student->email,

            'mobile' => $student->mobile,
            'alternate_mobile' => $student->alternate_mobile,

            'photo' => $student->photo
                ? asset('storage/' . $student->photo)
                : null,

            'address' => $student->address,

            // DATE FORMAT
            'dob' => $student->dob
                ? Carbon::parse($student->dob)->format('d M Y')
                : null,

            // BLOOD GROUP FORMAT
            'blood_group' => strtoupper(
                str_replace('_positive', '+', str_replace('_negative', '-', $student->blood_group))
            ),

            // GUARDIAN
            'guardian_name' => $student->guardian_name,
            'guardian_mobile' => $student->guardian_mobile,

            // EDUCATION
            'course' => $student->course,
            'batch' => $student->batch,
            'institute_name' => $student->institute_name,

            // JOINING DATE
            'joining_date' => $student->joining_date
                ? Carbon::parse($student->joining_date)->format('d M Y')
                : null,

            'status' => ucfirst($student->status),

            // DOCUMENTS
            'aadhaar_front' => $student->aadhaar_front
                ? asset('storage/' . $student->aadhaar_front)
                : null,

            'aadhaar_back' => $student->aadhaar_back
                ? asset('storage/' . $student->aadhaar_back)
                : null,

            'id_card_front' => $student->id_card_front
                ? asset('storage/' . $student->id_card_front)
                : null,

            'id_card_back' => $student->id_card_back
                ? asset('storage/' . $student->id_card_back)
                : null,

            // BRANCH
            'branch' => [
                'id' => optional($student->branch)->id,
                'name' => optional($student->branch)->name,
                'code' => optional($student->branch)->code,
                'address' => optional($student->branch)->address,
            ],

            // ROOM
            'room' => [
                'id' => optional($student->room)->id,
                'room_number' => optional($student->room)->room_number,
                'floor_number' => optional($student->room)->floor_number,
                'room_type' => ucfirst(optional($student->room)->room_type),
            ],

            // ALLOCATION
            'allocation' => [
                'allocation_date' => optional($student->activeAllocation)->allocation_date
                    ? Carbon::parse($student->activeAllocation->allocation_date)->format('d M Y')
                    : null,

                'status' => ucfirst(optional($student->activeAllocation)->status),
            ],

            // ACCESSORIES
            'accessories' => $student->accessories->map(function ($item) {

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => (int) $item->quantity,
                    'status' => ucfirst($item->status),
                ];

            })->values(),
        ];
    }
}