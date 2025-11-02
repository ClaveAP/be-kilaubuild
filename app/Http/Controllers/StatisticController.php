<?php

namespace App\Http\Controllers;

use App\Models\statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function createStatistic(Request $request){
        if (auth()->check()){   
            $incomingFields = $request->validate([
                'tahun_pengalaman' => 'required',
                'proyek_selesai' => 'required',
                'klien_puas' => 'required',
                'sebaran_kota' => 'required'
            ]);

            $incomingFields['tahun_pengalaman'] = strip_tags($incomingFields['tahun_pengalaman']);
            $incomingFields['proyek_selesai'] = strip_tags($incomingFields['proyek_selesai']);
            $incomingFields['klien_puas'] = strip_tags($incomingFields['klien_puas']);
            $incomingFields['sebaran_kota'] = strip_tags($incomingFields['sebaran_kota']);
            $incomingFields['user_id'] = auth()->id();
            statistic::create($incomingFields);
            return Redirect("/dashboard");
        }
        
        return redirect('/');  
    }

    public function showEditScreen(statistic $statis){
        if (auth()->id() == $statis['user_id']){
            return view('edit-statistic', ['statis' => $statis]);
        }
        
        return redirect('/');
    }

    public function updateStatistic(statistic $statis, Request $request){
        if (auth()->id() == $statis['user_id']){
            $incomingFields = $request->validate([
                'tahun_pengalaman' => 'required',
                'proyek_selesai' => 'required',
                'klien_puas' => 'required',
                'sebaran_kota' => 'required'
            ]);


            $incomingFields['tahun_pengalaman'] = strip_tags($incomingFields['tahun_pengalaman']);
            $incomingFields['proyek_selesai'] = strip_tags($incomingFields['proyek_selesai']);
            $incomingFields['klien_puas'] = strip_tags($incomingFields['klien_puas']);
            $incomingFields['sebaran_kota'] = strip_tags($incomingFields['sebaran_kota']);

            $statis->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteStatistic(statistic $statis){
        if (auth()->id() == $statis['user_id']){
            $statis->delete();
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }
}
