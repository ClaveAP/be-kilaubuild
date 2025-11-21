<?php

namespace App\Http\Controllers;

use App\Models\testimoni;
use Illuminate\Http\Request;

class TestimoniController extends Controller
{
    public function index(){
        // Ambil data terbaru
        $tstmns = testimoni::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $tstmns
        ], 200);
    }
    
    public function store(Request $request){
        // Validasi Input
        $incomingFields = $request->validate([
            'name' => 'required',
            'review' => 'required',
            'star' => 'required|integer|min:1|max:5', // Validasi bintang
        ]);

        // Sanitasi (Opsional, tapi bagus untuk keamanan)
        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['review'] = strip_tags($incomingFields['review']);
        $incomingFields['star'] = strip_tags($incomingFields['star']);
        
        // Ambil ID user yang sedang login (Admin)
        $incomingFields['user_id'] = auth()->id();
        
        // Simpan ke Database
        $tstmn = testimoni::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil dibuat',
            'data' => $tstmn
        ], 201);
    }

    public function update(testimoni $tstmn, Request $request){
        // Validasi
        $incomingFields = $request->validate([
            'name' => 'required',
            'review' => 'required',
            'star' => 'required|integer|min:1|max:5',
        ]);

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['review'] = strip_tags($incomingFields['review']);
        $incomingFields['star'] = strip_tags($incomingFields['star']);

        // Update Data
        $tstmn->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil diperbarui',
            'data' => $tstmn
        ], 200);
    }

    public function destroy(testimoni $tstmn){
        // Hapus Data
        $tstmn->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Testimoni berhasil dihapus',
            'data' => null
        ], 200);
    }
}