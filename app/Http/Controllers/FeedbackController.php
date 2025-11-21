<?php

namespace App\Http\Controllers;

use App\Models\feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function sendFeedback(Request $request){
        $incomingFields = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'no_telp' => 'nullable',
            'feedback' => 'required'
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);
        $incomingFields['no_telp'] = strip_tags($incomingFields['no_telp']);
        $incomingFields['feedback'] = strip_tags($incomingFields['feedback']);
        
        $data = feedback::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback berhasil dikirim',
            'data' => $data
        ], 201);
    }
}