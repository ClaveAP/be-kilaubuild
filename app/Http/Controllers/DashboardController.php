<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Faq;
use App\Models\User;
use App\Models\contact;
use App\Models\feedback;
use App\Models\statistic;
use App\Models\testimoni;
use App\Models\projectDone;
use App\Models\InstagramPost;
use App\Models\desainInterior;
use App\Models\ongoingProjects;

class DashboardController extends Controller
{
    /**
     * Mengambil ringkasan data untuk halaman utama Dashboard Admin.
     * Endpoint: GET /api/dashboard-data
     */
    public function index()
    {
        $counts = [
            'projects_done'     => projectDone::count(),
            'ongoing_projects'  => ongoingProjects::count(),
            'interior_designs'  => desainInterior::count(),
            'instagram_posts'   => InstagramPost::count(),
            'testimonies'       => testimoni::count(),
            'faqs'              => Faq::count(),
            'feedbacks'         => feedback::count(),
        ];
        
        $statistic = statistic::latest()->first();
        $contact = contact::latest()->first();

        // Ambil 5 feedback terbaru untuk list notifikasi
        $latestFeedbacks = feedback::latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard berhasil diambil',
            'data' => [
                'summary_counts' => $counts,
                'active_statistic' => $statistic,
                'active_contact' => $contact,
                'recent_feedbacks' => $latestFeedbacks
            ]
        ], 200);
    }
}