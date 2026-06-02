<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel\Notice;
use Carbon\Carbon;

class NoticesApiController extends Controller
{
    /**
     * ALL NOTICES
     */
    public function index()
    {
        $notices = Notice::latest('notice_date')
            ->get()
            ->map(function ($notice) {

                return $this->formatNotice($notice);
            });

        return response()->json([

            'status' => true,
            'message' => 'Notices fetched successfully',

            'data' => $notices
        ]);
    }

    /**
     * LATEST NOTICES
     */
    public function latestNotices()
    {
        $notices = Notice::latest('notice_date')
            ->take(5)
            ->get()
            ->map(function ($notice) {

                return $this->formatNotice($notice);
            });

        return response()->json([

            'status' => true,
            'message' => 'Latest notices fetched successfully',

            'data' => $notices
        ]);
    }

    /**
     * SINGLE NOTICE
     */
    public function show($id)
    {
        $notice = Notice::find($id);

        if (!$notice) {

            return response()->json([
                'status' => false,
                'message' => 'Notice not found'
            ], 404);
        }

        return response()->json([

            'status' => true,
            'message' => 'Notice fetched successfully',

            'data' => $this->formatNotice($notice)
        ]);
    }

    /**
     * FORMAT NOTICE
     */
    private function formatNotice($notice)
    {
        return [

            'id' => $notice->id,

            'title' => $notice->title ?? '',

            'description' => $notice->description ?? '',

            'notice_date' => $notice->notice_date
                ? Carbon::parse($notice->notice_date)->format('d M Y')
                : null,

            'day' => $notice->notice_date
                ? Carbon::parse($notice->notice_date)->format('l')
                : null,

            'status' => ucfirst($notice->status ?? 'active'),

            'attachment' => $notice->attachment
                ? asset('storage/' . $notice->attachment)
                : null,
        ];
    }
}