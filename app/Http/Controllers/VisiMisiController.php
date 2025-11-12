<?php

namespace App\Http\Controllers;

use App\Models\visiMisi;
use App\Models\visionMission;
use Illuminate\Http\Request;

class VisiMisiController extends Controller
{
    public function createVisiMisi(Request $request){
        if (auth()->check()){   
            $incomingFields = $request->validate([
                'visi' => 'required',
                'misi' => 'required'
            ]);

            $incomingFields['visi'] = strip_tags($incomingFields['visi']);
            $incomingFields['misi'] = strip_tags($incomingFields['misi']);
            $incomingFields['user_id'] = auth()->id();
            visionMission::create($incomingFields);
            return Redirect("/dashboard");
        }
        
        return redirect('/');
    }

    public function showEditScreen(visionMission $VM){
        if (auth()->id() == $VM['user_id']){
            return view('edit-visi-misi', ['VM' => $VM]);
        }
        
        return redirect('/');
    }

    public function updateVisiMisi(visionMission $VM, Request $request){
        if (auth()->id() == $VM['user_id']){
            $incomingFields = $request->validate([
                'visi' => 'required',
                'misi' => 'required'
            ]);

            $incomingFields['visi'] = strip_tags($incomingFields['visi']);
            $incomingFields['misi'] = strip_tags($incomingFields['misi']);

            $VM->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteVisiMisi(visionMission $VM){
       if (auth()->id() == $VM['user_id']){
            $VM->delete();

            return redirect('/dashboard');
        }
        
        return redirect('/');
    }
}
