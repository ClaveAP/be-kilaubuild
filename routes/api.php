<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController ;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\ProjectDoneController;
use App\Http\Controllers\InstagramPostController;
use App\Http\Controllers\DesainInteriorController;
use App\Http\Controllers\OngoingProjectController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================================================
// 1. PUBLIC ROUTES (Bisa diakses siapa saja / Tamu)
// ============================================================================

// Auth (Login & Register tidak butuh token)
// Route::post('/register', [AdminController::class, 'register']);
Route::post('/login', [AdminController::class, 'login']);

// Feedback (Public bisa kirim feedback)
Route::post('/send-feedback', [FeedbackController::class, 'sendFeedback']);

// GET Methods (Public bisa melihat data)
Route::get('/statistic', [StatisticController::class, 'index']); // Hapus param {statis}
Route::get('/post', [InstagramPostController::class, 'index']); // Hapus param {post}
Route::get('/faq', [FaqController::class, 'index']);
Route::get('/faq/{faq}', [FaqController::class, 'show']);
Route::get('/testimoni', [TestimoniController::class, 'index']); // Hapus param {tstmn}
Route::get('/ongoing-project', [OngoingProjectController::class, 'index']); // Hapus param {OP}
Route::get('/project-done', [ProjectDoneController::class, 'index']); // Perbaiki POST jadi GET
Route::get('/desain-interior', [DesainInteriorController::class, 'index']);
Route::get('/contact', [ContactController::class, 'index']); // Hapus param {cont}


// ============================================================================
// 2. PROTECTED ROUTES (Hanya Admin / User Login)
// ============================================================================
// Menggunakan middleware auth:sanctum (pastikan Laravel Sanctum sudah terinstall)
Route::middleware('auth:sanctum')->group(function () {

    // Auth User Info & Logout
    Route::get('/user', [AdminController::class, 'checkAuth']); // Ganti redirectDashboard jadi checkAuth
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::post('/change-password', [AdminController::class, 'changePassword']);

    // ==== Dashboard Data ====
    Route::get('/dashboard-data', [DashboardController::class, 'index']);

    // === Statistic ===
    Route::post('/statistic', [StatisticController::class, 'store']);
    Route::put('/statistic/{statis}', [StatisticController::class, 'update']);
    Route::delete('/statistic/{statis}', [StatisticController::class, 'destroy']);

    // === Instagram Posts ===
    Route::post('/post', [InstagramPostController::class, 'store']);
    Route::put('/post/{post}', [InstagramPostController::class, 'update']); 
    Route::delete('/post/{post}', [InstagramPostController::class, 'destroy']);

    // === FAQ ===
    Route::post('/faq', [FaqController::class, 'store']);
    Route::put('/faq/{faq}', [FaqController::class, 'update']);
    Route::delete('/faq/{faq}', [FaqController::class, 'destroy']);

    // === Testimoni ===
    Route::post('/testimoni', [TestimoniController::class, 'store']);
    Route::put('/testimoni/{tstmn}', [TestimoniController::class, 'update']);
    Route::delete('/testimoni/{tstmn}', [TestimoniController::class, 'destroy']);

    // === Ongoing Project ===
    Route::post('/ongoing-project', [OngoingProjectController::class, 'store']);
    Route::put('/ongoing-project/{OP}', [OngoingProjectController::class, 'update']);
    Route::delete('/ongoing-project/{OP}', [OngoingProjectController::class, 'destroy']);

    // === Project Done ===
    Route::post('/project-done', [ProjectDoneController::class, 'store']); 
    Route::put('/project-done/{PD}', [ProjectDoneController::class, 'update']);
    Route::delete('/project-done/{PD}', [ProjectDoneController::class, 'destroy']);

    // === Desain Interior ===
    Route::post('/desain-interior', [DesainInteriorController::class, 'store']);
    Route::post('/desain-interior/{id}', [DesainInteriorController::class, 'update']); 
    Route::put('/desain-interior/{id}', [DesainInteriorController::class, 'update']);
    Route::delete('/desain-interior/{id}', [DesainInteriorController::class, 'destroy']);

    // === Contact ===
    Route::post('/contact', [ContactController::class, 'store']);
    Route::put('/contact/{cont}', [ContactController::class, 'update']);
    Route::delete('/contact/{cont}', [ContactController::class, 'destroy']);
});