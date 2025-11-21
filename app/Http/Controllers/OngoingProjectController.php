<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ongoingProjects;
use Illuminate\Support\Facades\Storage;

class OngoingProjectController extends Controller
{
    public function index(){
        $OPs = ongoingProjects::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $OPs
        ], 200);
    }
    
    public function store(Request $request){
        // Cek Auth
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
           
        $incomingFields = $request->validate([
            'name' => 'required',
            'loc' => 'required',
            'persen' => 'required',
            'image' => 'required|image|max:5120' // Tambahkan validasi image
        ]);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ongoing_project', 'public');
            // Pastikan ImageController ada dan method static compressImage tersedia
            ImageController::compressImage($imagePath); 
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['loc'] = strip_tags($incomingFields['loc']);
        $incomingFields['persen'] = strip_tags($incomingFields['persen']);
        $incomingFields['user_id'] = auth()->id();
        
        $OP = ongoingProjects::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Project berhasil ditambahkan',
            'data' => $OP
        ], 201); // 201 Created
    }

    public function update(ongoingProjects $OP, Request $request){
        if (auth()->id() !== $OP->user_id){
            return response()->json(['success' => false, 'message' => 'Forbidden Access'], 403);
        }

        $incomingFields = $request->validate([
            'name' => 'required',
            'loc' => 'required',
            'persen' => 'required',
            'image' => 'sometimes|image|nullable|max:5120'
        ]);

        if($request->hasFile('image')) {
            if($OP->image) Storage::disk('public')->delete($OP->image);
            $imagePath = $request->file('image')->store('ongoing_project', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['loc'] = strip_tags($incomingFields['loc']);
        $incomingFields['persen'] = strip_tags($incomingFields['persen']);

        $OP->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => "Ongoing Project berhasil diperbarui",
            'data' => $OP
        ], 200);
    }

    public function destroy(ongoingProjects $OP){
        if (auth()->id() !== $OP->user_id){
            return response()->json(['success' => false, 'message' => 'Forbidden Access'], 403);
        }

        if($OP->image) Storage::disk('public')->delete($OP->image);
        $OP->delete();
    
        return response()->json([
            'success' => true,
            'message' => "Ongoing Project berhasil dihapus",
            'data' => null // Data dihapus, return null atau ID nya saja
        ], 200);
    }
}