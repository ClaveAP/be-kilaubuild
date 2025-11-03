<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DesainInteriorController;
use App\Http\Controllers\OngoingProjectController;
use App\Http\Controllers\ProjectDoneController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TestimonyController;
use App\Models\desainInterior;
use App\Models\Faq;
use App\Models\InstagramPost;
use App\Models\ongoingProjects;
use App\Models\projectDone;
use App\Models\service;
use App\Models\statistic;
use App\Models\testimony;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstagramPostController;

Route::get('/', function () {
    $posts = InstagramPost::all();
    return view('home', ['posts' => $posts]);
});

Route::get('/dashboard', function () {
    $posts = InstagramPost::latest()->get();
    $srvcs = service::latest()->get();
    $faqs = Faq::latest()->get();
    $tstmns = testimony::latest()->get();
    $PDs = projectDone::latest()->get();
    $OPs = ongoingProjects::latest()->get();
    $DIs = desainInterior::latest()->get();
    $statis = statistic::all();
    
    return view('dashboard', [
        'posts' => $posts,
        'srvcs' => $srvcs,
        'faqs' => $faqs,
        'tstmns' => $tstmns,
        'PDs' => $PDs,
        'OPs' => $OPs,
        'DIs' => $DIs,
        'statis' => $statis
    ]);
});

Route::POST('/register', [AdminController::class,'register']);
Route::POST('/login', [AdminController::class, 'login']);
Route::POST('/logout', [AdminController::class, 'logout']);
Route::POST('/dashboard', [AdminController::class, 'redirectDashboard']);

// Instagram Posts
Route::post('/create-post', [InstagramPostController::class, 'createPost']);
Route::get('/edit-post/{post}', [InstagramPostController::class, 'showEditScreen']);
Route::put('/edit-post/{post}', [InstagramPostController::class, 'updatePost']);
Route::delete('/delete-post/{post}', [InstagramPostController::class, 'deletePost']);

// Services
Route::post('/create-service', [ServiceController::class, 'createService']);
Route::get('/edit-service/{srvc}', [ServiceController::class, 'showEditScreen']);
Route::put('/edit-service/{srvc}', [ServiceController::class, 'updateService']);
Route::delete('/delete-service/{srvc}', [ServiceController::class, 'deleteService']);

// Statistic
Route::post('/create-statistic', [StatisticController::class, 'createStatistic']);
Route::get('/edit-statistic/{statis}', [StatisticController::class, 'showEditScreen']);
Route::put('/edit-statistic/{statis}', [StatisticController::class, 'updateStatistic']);
Route::delete('/delete-statistic/{statis}', [StatisticController::class, 'deleteStatistic']);

// FAQ
Route::post('/create-faq', [FaqController::class, 'createFAQ']);
Route::get('/edit-faq/{faq}', [FaqController::class, 'showEditScreen']);
Route::put('/edit-faq/{faq}', [FaqController::class, 'updateFAQ']);
Route::delete('/delete-faq/{faq}', [FaqController::class, 'deleteFAQ']);

// Testimonys
Route::post('/create-testimony', [TestimonyController::class, 'createTstmn']);
Route::get('/edit-testimony/{tstmn}', [TestimonyController::class, 'showEditScreen']);
Route::put('/edit-testimony/{tstmn}', [TestimonyController::class, 'updateTstmn']);
Route::delete('/delete-testimony/{tstmn}', [TestimonyController::class, 'deleteTstmn']);

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
Route::post('/create-desain-interior', [DesainInteriorController::class, 'createDI']);
Route::get('/edit-desain-interior/{DI}', [DesainInteriorController::class, 'showEditScreen']);
Route::put('/edit-desain-interior/{DI}', [DesainInteriorController::class, 'updateDI']);
Route::delete('/delete-desain-interior/{DI}', [DesainInteriorController::class, 'deleteDI']);

// Contact
Route::post('/create-contact', [ContactController::class, 'createContact']);
Route::get('/edit-contact/{cont}', [ContactController::class, 'showEditScreen']);
Route::put('/edit-contact/{cont}', [ContactController::class, 'updateContact']);
Route::delete('/delete-contact/{cont}', [ContactController::class, 'deleteContact']);