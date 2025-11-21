<?php

namespace App\Http\Controllers;

use App\Models\desainInterior;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DesainInteriorController extends Controller
{
    public function index(){
        $interiors = desainInterior::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $interiors
        ], 200);
    }
    
    public function store(Request $request){
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }   

        $incomingFields = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:5120'
        ]);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('design_interior', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['user_id'] = auth()->id();
        
        $interior = desainInterior::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Design Interior berhasil dibuat',
            'data' => $interior
        ], 201);
    }

    // ✅ MANUAL FIND - UPDATE METHOD
    public function update(Request $request, $id){
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Manual find by ID
        $interior = desainInterior::find($id);
        
        // Check if exists
        if (!$interior) {
            return response()->json([
                'success' => false, 
                'message' => 'Design Interior tidak ditemukan'
            ], 404);
        }

        // Check authorization
        if (auth()->id() !== $interior->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $incomingFields = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'sometimes|image|nullable|max:5120'
        ]);

        if($request->hasFile('image')) {
            if($interior->image) Storage::disk('public')->delete($interior->image);
            $imagePath = $request->file('image')->store('design_interior', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);

        $interior->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Design Interior berhasil diperbarui',
            'data' => $interior
        ], 200);
    }

    // ✅ MANUAL FIND - DESTROY METHOD
    public function destroy($id){
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Manual find by ID
        $interior = desainInterior::find($id);
        
        // Check if exists
        if (!$interior) {
            return response()->json([
                'success' => false, 
                'message' => 'Design Interior tidak ditemukan'
            ], 404);
        }

        // Check authorization
        if (auth()->id() !== $interior->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if($interior->image) Storage::disk('public')->delete($interior->image);
        $interior->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Design Interior berhasil dihapus',
        ], 200);
    }
}