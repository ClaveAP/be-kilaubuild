<?php

use App\Models\contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/contact/phone', function () {
    $contact = contact::first(); // Ambil baris pertama (atau sesuaikan query)
    return response()->json(['no_telp' => $contact->no_telp]);
});
