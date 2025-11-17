<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstagramPost;
use Illuminate\Support\Facades\Storage;

class InstagramPostController extends Controller
{
    public function createPost(Request $request){
        if (auth()->check()){       
            $incomingFields = $request->validate([
                'title' => 'required',
                'instagram_url' => 'required',
                'image' => 'required'
            ]);

            if($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('instagram_posts', 'public');
                $incomingFields['image'] = $imagePath;
            }

            $incomingFields['title'] = strip_tags($incomingFields['title']);
            $incomingFields['instagram_url'] = filter_var($incomingFields['instagram_url']);
            $incomingFields['user_id'] = auth()->id();
            $incomingFields['is_published'] = $request->has('di_homepage');
            
            InstagramPost::create($incomingFields);
            return Redirect("/dashboard");
        }
        
        return redirect('/');
    }

    public function showEditScreen(InstagramPost $post){
        if (auth()->id() == $post['user_id']){
            return view('edit-post', ['post' => $post]);
        }
        
        return redirect('/');
    }

    public function updatePost(InstagramPost $post, Request $request){
        if (auth()->id() == $post['user_id']){
            $incomingFields = $request->validate([
                'title' => 'required',
                'instagram_url' => 'required'
            ]);

            if($request->hasFile('image')) {
                Storage::disk('public')->delete($post->image);
                $imagePath = $request->file('image')->store('instagram_posts', 'public');
                $incomingFields['image'] = $imagePath;
            }

            $incomingFields['title'] = strip_tags($incomingFields['title']);
            $incomingFields['instagram_url'] = strip_tags($incomingFields['instagram_url']);
            $incomingFields['is_published'] = $request->has('di_homepage');

            $post->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deletePost(InstagramPost $post){
        if (auth()->id() == $post['user_id']){
            $post->delete();
            Storage::disk('public')->delete($post->image);
        }
        
        return redirect('/dashboard');
    }

    public function viewToHome(InstagramPost $post){
        if (auth()->id() == $post['user_id']){
            $post->update([
                'di_homepage' => !$post->di_homepage
            ]);
        }
        
        return redirect('/dashboard');
    }
}