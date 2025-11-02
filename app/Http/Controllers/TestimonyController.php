<?php

namespace App\Http\Controllers;

use App\Models\testimony;
use Illuminate\Http\Request;

class TestimonyController extends Controller
{
    public function createTstmn(Request $request){
        $incomingFields = $request->validate([
            'name' => 'required',
            'review' => 'required',
            'star' => 'required',
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['review'] = strip_tags($incomingFields['review']);
        $incomingFields['star'] = strip_tags($incomingFields['star']);
        $incomingFields['user_id'] = auth()->id();
        testimony::create($incomingFields);
        return Redirect("/dashboard");
    }

    public function showEditScreen(testimony $tstmn){
        if (auth()->id() == $tstmn['user_id']){
            return view('edit-testimony', ['tstmn' => $tstmn]);
        }
        
        return redirect('/');
    }

    public function updateTstmn(testimony $tstmn, Request $request){
        if (auth()->id() == $tstmn['user_id']){
            $incomingFields = $request->validate([
                'name' => 'required',
                'review' => 'required',
                'star' => 'required',
            ]);


            $incomingFields['name'] = strip_tags($incomingFields['name']);
            $incomingFields['review'] = strip_tags($incomingFields['review']);
            $incomingFields['star'] = strip_tags($incomingFields['star']);

            $tstmn->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteTstmn(testimony $tstmn){
        if (auth()->id() == $tstmn['user_id']){
            $tstmn->delete();
    
        }
        
        return redirect('/dashboard');
    }
}
