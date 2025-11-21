<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstagramPost;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ImageController;

class InstagramPostController extends Controller
{
    public function index(){
        $posts = InstagramPost::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $posts
        ], 200);
    }
    
    public function store(Request $request){
        if (!auth()->check()){
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }       

        $incomingFields = $request->validate([
            'title' => 'required',
            'instagram_url' => 'required|url',
            'image' => 'required|image'
        ]);

        if($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('instagram_posts', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        
        $incomingFields['instagram_url'] = strip_tags($incomingFields['instagram_url']); 
        $incomingFields['user_id'] = auth()->id();
        
        $post = InstagramPost::create($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Post berhasil dibuat',
            'data' => $post
        ], 201);
    }

    public function update(InstagramPost $post, Request $request){
        if (auth()->id() !== $post->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $incomingFields = $request->validate([
            'title' => 'required',
            'instagram_url' => 'required|url',
            'image' => 'sometimes|image|nullable'
        ]);

        if($request->hasFile('image')) {
            if($post->image) Storage::disk('public')->delete($post->image);
            $imagePath = $request->file('image')->store('instagram_posts', 'public');
            ImageController::compressImage($imagePath);
            $incomingFields['image'] = $imagePath;
        }

        $incomingFields['title'] = strip_tags($incomingFields['title']);
        $incomingFields['instagram_url'] = strip_tags($incomingFields['instagram_url']);
        
       
        if($request->has('di_homepage')){
            $incomingFields['di_homepage'] = true; 
        }

        $post->update($incomingFields);
        
        return response()->json([
            'success' => true,
            'message' => 'Post berhasil diperbarui',
            'data' => $post
        ], 200);
    }

    public function destroy(InstagramPost $post){
        if (auth()->id() !== $post->user_id){
             return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        if($post->image) Storage::disk('public')->delete($post->image);
        $post->delete();
    
        return response()->json([
            'success' => true,
            'message' => 'Post berhasil dihapus',
        ], 200);
    }
}