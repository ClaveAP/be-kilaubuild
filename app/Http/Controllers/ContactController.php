<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function createContact(Request $request){
        if (auth()->check()){   
            $incomingFields = $request->validate([
                'no_telp' => 'required',
                'alamat' => 'required',
                'link_gmaps' => 'required',
                'email' => 'required',
                'url_instagram' => 'nullable',
                'url_facebook' => 'nullable',
                'url_threads' => 'nullable',
                'url_tiktok' => 'nullable',
                'url_youtube' => 'nullable',
                'url_twitter' => 'nullable',
            ]);

            $incomingFields['no_telp'] = strip_tags($incomingFields['no_telp']);
            $incomingFields['alamat'] = strip_tags($incomingFields['alamat']);
            $incomingFields['link_gmaps'] = filter_var($incomingFields['link_gmaps']);
            $incomingFields['email'] = strip_tags($incomingFields['email']);
            $incomingFields['url_instagram'] = filter_var($incomingFields['url_instagram']);
            $incomingFields['url_facebook'] = filter_var($incomingFields['url_facebook']);
            $incomingFields['url_threads'] = filter_var($incomingFields['url_threads']);
            $incomingFields['url_tiktok'] = filter_var($incomingFields['url_tiktok']);
            $incomingFields['url_youtube'] = filter_var($incomingFields['url_youtube']);
            $incomingFields['url_twitter'] = filter_var($incomingFields['url_twitter']);
            $incomingFields['user_id'] = auth()->id();
            contact::create($incomingFields);
            return Redirect("/dashboard");
        }
        
        return redirect('/');  
    }

    public function showEditScreen(contact $cont){
        if (auth()->id() !== $cont['user_id']){
            return redirect('/');
        }
        
        return view('edit-contact', ['cont' => $cont]);
    }

    public function updateContact(contact $cont, Request $request){
        if (auth()->user()->id == $cont['user_id']){
            $incomingFields = $request->validate([
                'no_telp' => 'required',
                'alamat' => 'required',
                'link_gmaps' => 'required',
                'email' => 'required',
                'url_instagram' => 'nullable',
                'url_facebook' => 'nullable',
                'url_threads' => 'nullable',
                'url_tiktok' => 'nullable',
                'url_youtube' => 'nullable',
                'url_twitter' => 'nullable',
            ]);

            $incomingFields['no_telp'] = strip_tags($incomingFields['no_telp']);
            $incomingFields['alamat'] = strip_tags($incomingFields['alamat']);
            $incomingFields['link_gmaps'] = filter_var($incomingFields['link_gmaps']);
            $incomingFields['email'] = strip_tags($incomingFields['email']);
            $incomingFields['url_instagram'] = filter_var($incomingFields['url_instagram']);
            $incomingFields['url_facebook'] = filter_var($incomingFields['url_facebook']);
            $incomingFields['url_threads'] = filter_var($incomingFields['url_threads']);
            $incomingFields['url_tiktok'] = filter_var($incomingFields['url_tiktok']);
            $incomingFields['url_youtube'] = filter_var($incomingFields['url_youtube']);
            $incomingFields['url_twitter'] = filter_var($incomingFields['url_twitter']);

            $cont->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteContact(contact $cont){
        if (auth()->id() == $cont['user_id']){
            $cont->delete();
            
        }
        
        return redirect('/dashboard');
    }
}
