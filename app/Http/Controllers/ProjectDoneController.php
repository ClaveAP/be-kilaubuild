<?php

namespace App\Http\Controllers;

use App\Models\projectDone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDoneController extends Controller
{
    public function index(){
        $PDs = projectDone::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $PDs
        ], 200);
    }
    
    public function store(Request $request){
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }   

        $incomingFields = $request->validate([
            'name' => 'required',
            'desc' => 'required',
            'year' => 'required',
            'image' => 'required|image|max:5120'
        ]);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('project_done', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['desc'] = strip_tags($incomingFields['desc']);
        $incomingFields['year'] = strip_tags($incomingFields['year']);
        $incomingFields['user_id'] = auth()->id();
        
        $PD = projectDone::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Project Done berhasil dibuat',
            'data' => $PD
        ], 201);
    }

    public function update(projectDone $PD, Request $request){
        if (auth()->id() !== $PD->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $incomingFields = $request->validate([
            'name' => 'required',
            'desc' => 'required',
            'year' => 'required',
            'image' => 'sometimes|image|nullable|max:5120'
        ]);

        if($request->hasFile('image')) {
            if($PD->image) Storage::disk('public')->delete($PD->image);
            $imagePath = $request->file('image')->store('project_done', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['desc'] = strip_tags($incomingFields['desc']);
        $incomingFields['year'] = strip_tags($incomingFields['year']);

        $PD->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Project Done berhasil diperbarui',
            'data' => $PD
        ], 200);
    }

    public function destroy(projectDone $PD){
        if (auth()->id() !== $PD->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        // REVISI: $$PD menjadi $PD
        if($PD->image) Storage::disk('public')->delete($PD->image);
        $PD->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Project Done berhasil dihapus',
        ], 200);
    }
}