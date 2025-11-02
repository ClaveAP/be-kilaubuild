<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function createFAQ(Request $request){
        $incomingFields = $request->validate([
            'question' => 'required',
            'answer' => 'required'
        ]);

        $incomingFields['question'] = strip_tags($incomingFields['question']);
        $incomingFields['answer'] = filter_var($incomingFields['answer']);
        $incomingFields['user_id'] = auth()->id();
        Faq::create($incomingFields);
        return Redirect("/dashboard");
    }

    public function showEditScreen(Faq $faq){
        if (auth()->id() == $faq['user_id']){
            return view('edit-faq', ['faq' => $faq]);
        }
        
        return redirect('/');
    }

    public function updateFAQ(Faq $faq, Request $request){
        if (auth()->id() == $faq['user_id']){
            $incomingFields = $request->validate([
                'question' => 'required',
                'answer' => 'required'
            ]);

            $incomingFields['question'] = strip_tags($incomingFields['question']);
            $incomingFields['answer'] = strip_tags($incomingFields['answer']);

            $faq->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteFAQ(Faq $faq){
       if (auth()->user()->id() == $faq['user_id']){
            $faq->delete();

            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    
}
