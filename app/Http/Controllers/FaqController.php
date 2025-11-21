<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * index: Get all FAQs
     * show: Get a specific FAQ by ID
     * store: Create a new FAQ
     * update: Update an existing FAQ by ID
     * destroy: Delete an FAQ by ID
     */

    public function index()
    {
        $faqs = Faq::get();
        return response()->json([
            "success" => true,
            "data" => $faqs,
        ], 200);
    }

    // Fungsi baru untuk mendapatkan FAQ berdasarkan ID
    public function show(Faq $faq)
    {
        return response()->json([
            "success" => true,
            "data" => $faq,
        ], 200);
    }

    public function store(Request $request)
    {
        $incomingFields = $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        $incomingFields['question'] = strip_tags($incomingFields['question']);
        $incomingFields['answer'] = strip_tags($incomingFields['answer']);
        // Asumsi user_id adalah 1 karena tidak ada autentikasi API di sini
        $incomingFields['user_id'] = 1; 
        $faq = Faq::create($incomingFields);

        return response()->json([
            "success" => true,
            "data" => $faq,
        ], 201);
    }

    // Fungsi baru untuk mengupdate FAQ
    public function update(Request $request, Faq $faq)
    {
        // Catatan: Di versi non-API di web.php ada cek auth()->id() == $faq['user_id'],
        // tapi di sini diabaikan untuk API sederhana.
        $incomingFields = $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        $incomingFields['question'] = strip_tags($incomingFields['question']);
        $incomingFields['answer'] = strip_tags($incomingFields['answer']);

        $faq->update($incomingFields);

        return response()->json([
            "success" => true,
            "message" => "FAQ berhasil diperbarui.",
            "data" => $faq,
        ], 200);
    }

    // Fungsi baru untuk menghapus FAQ
    public function destroy(Faq $faq)
    {
        // Catatan: Di versi non-API di web.php ada cek auth()->id() == $faq['user_id'],
        // tapi di sini diabaikan untuk API sederhana.
        $faq->delete();

        return response()->json([
            "success" => true,
            "message" => "FAQ berhasil dihapus.",
        ], 200);
    }
}