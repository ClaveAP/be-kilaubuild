<?php

use App\Models\Faq;
use App\Models\benefit;
use App\Models\contact;
use App\Models\service;
use App\Models\statistic;
use App\Models\testimony;
use App\Models\projectDone;
use App\Models\ownerProfile;
use App\Models\InstagramPost;
use App\Models\visionMission;
use App\Models\desainInterior;
use App\Models\ongoingProjects;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TestimoniController;
use App\Http\Controllers\ProjectDoneController;
use App\Http\Controllers\InstagramPostController;
use App\Http\Controllers\DesainInteriorController;
use App\Http\Controllers\OngoingProjectController;
use App\Models\testimoni;

Route::get('/', function () {
    $posts = InstagramPost::all();
    return view('home', ['posts' => $posts]);
});

Route::get('/contact/phone', function () {
    $contact = contact::first(); // Ambil baris pertama (atau sesuaikan query)
    return response()->json(['no_telp' => $contact->no_telp]);
});

Route::get('/dashboard', function () {
    $posts = InstagramPost::latest()->get();
    $srvcs = service::latest()->get();
    $faqs = Faq::latest()->get();
    $tstmns = testimoni::latest()->get();
    $PDs = projectDone::latest()->get();
    $OPs = ongoingProjects::latest()->get();
    $DIs = desainInterior::latest()->get();
    $statis = statistic::all();
    $cont = contact::all();
    $owp = ownerProfile::all();
    $VM = visionMission::all();
    $bnfts = benefit::all();
    
    return view('dashboard', [
        'posts' => $posts,
        'srvcs' => $srvcs,
        'faqs' => $faqs,
        'tstmns' => $tstmns,
        'PDs' => $PDs,
        'OPs' => $OPs,
        'DIs' => $DIs,
        'statis' => $statis,
        'cont' => $cont,
        'owp' => $owp,
        'VM' => $VM,
        'bnfts' => $bnfts
    ]);
});

Route::POST('/register', [AdminController::class,'register']);
Route::POST('/login', [AdminController::class, 'login']);
Route::POST('/logout', [AdminController::class, 'logout']);
Route::POST('/dashboard', [AdminController::class, 'redirectDashboard']);
Route::POST('/send-feedback', [FeedbackController::class, 'sendFeedback']);

// Instagram Posts
Route::post('/create-post', [InstagramPostController::class, 'createPost']);
Route::get('/edit-post/{post}', [InstagramPostController::class, 'showEditScreen']);
Route::put('/edit-post/{post}', [InstagramPostController::class, 'updatePost']);
Route::delete('/delete-post/{post}', [InstagramPostController::class, 'deletePost']);
Route::patch('/toggle-home-post/{post}', [InstagramPostController::class, 'viewToHome']);

// Statistic
Route::post('/create-statistic', [StatisticController::class, 'createStatistic']);
Route::get('/edit-statistic/{statis}', [StatisticController::class, 'showEditScreen']);
Route::put('/edit-statistic/{statis}', [StatisticController::class, 'updateStatistic']);
Route::delete('/delete-statistic/{statis}', [StatisticController::class, 'deleteStatistic']);

// FAQ

// [GET] 200 be.facebook.com/api/faq
// [GET] 200 be.facebook.com/api/faq/1
// [POST] 201 be.facebook.com/api/faq
// [PUT] 200 be.facebook.com/api/faq/1
// [DELETE] 200 be.facebook.com/api/faq/1
Route::get('/faq', [FaqController::class, 'index']);
Route::post('/faq', [FaqController::class, 'store']);
Route::put('/faq/{faq}', [FaqController::class, 'update']);
Route::delete('/faq/{faq}', [FaqController::class, 'destroy']);

// Testimoni
Route::post('/create-testimoni', [TestimoniController::class, 'createTstmn']);
Route::get('/edit-testimoni/{tstmn}', [TestimoniController::class, 'showEditScreen']);
Route::put('/edit-testimoni/{tstmn}', [TestimoniController::class, 'updateTstmn']);
Route::delete('/delete-testimoni/{tstmn}', [TestimoniController::class, 'deleteTstmn']);

// Ongoing Project
Route::post('/create-ongoing-project', [OngoingProjectController::class, 'createOP']);
Route::get('/edit-ongoing-project/{OP}', [OngoingProjectController::class, 'showEditScreen']);
Route::put('/edit-ongoing-project/{OP}', [OngoingProjectController::class, 'updateOP']);
Route::delete('/delete-ongoing-project/{OP}', [OngoingProjectController::class, 'deleteOP']);

// Project Done
Route::post('/create-project-done', [ProjectDoneController::class, 'createPD']);
Route::get('/edit-project-done/{PD}', [ProjectDoneController::class, 'showEditScreen']);
Route::put('/edit-project-done/{PD}', [ProjectDoneController::class, 'updatePD']);
Route::delete('/delete-project-done/{PD}', [ProjectDoneController::class, 'deletePD']);

// Desain Interior
Route::post('desain-interior', [DesainInteriorController::class, 'index']);
Route::get('/desain-interior/{DI}', [DesainInteriorController::class, 'store']);
Route::put('/desain-interior/{DI}', [DesainInteriorController::class, 'update']);
Route::delete('/delete-desain-interior/{DI}', [DesainInteriorController::class, 'destroy']);

// Contact
Route::post('/create-contact', [ContactController::class, 'createContact']);
Route::get('/edit-contact/{cont}', [ContactController::class, 'showEditScreen']);
Route::put('/edit-contact/{cont}', [ContactController::class, 'updateContact']);
Route::delete('/delete-contact/{cont}', [ContactController::class, 'deleteContact']);