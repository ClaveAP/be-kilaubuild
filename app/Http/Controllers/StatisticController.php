<?php

namespace App\Http\Controllers;

use App\Models\statistic;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(){
        $statis = statistic::all();

        return response()->json([
            'success' => true,
            'data' => $statis
        ], 200);
    }
    
    public function store(Request $request){
        if (!auth()->check()){
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }   

        $incomingFields = $request->validate([
            'tahun_pengalaman' => 'required',
            'proyek_selesai' => 'required',
            'klien_puas' => 'required',
            'sebaran_kota' => 'required'
        ]);

        // Sanitasi
        foreach($incomingFields as $key => $value) {
            $incomingFields[$key] = strip_tags($value);
        }
        
        $incomingFields['user_id'] = auth()->id();
        $statis = statistic::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => "Statistik berhasil dibuat",
            'data' => $statis
        ], 201);
    }

    public function update(statistic $statis, Request $request){
        if (auth()->id() !== $statis->user_id){
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $incomingFields = $request->validate([
            'tahun_pengalaman' => 'required',
            'proyek_selesai' => 'required',
            'klien_puas' => 'required',
            'sebaran_kota' => 'required'
        ]);

        foreach($incomingFields as $key => $value) {
            $incomingFields[$key] = strip_tags($value);
        }

        $statis->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => "Statistik berhasil diperbarui",
            'data' => $statis
        ], 200);
    }

    public function destroy(statistic $statis){
        if (auth()->id() !== $statis->user_id){
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }
        
        $statis->delete();
        
        return response()->json([
            'success' => true,
            'message' => "Statistik berhasil dihapus"
        ], 200);
    }
}