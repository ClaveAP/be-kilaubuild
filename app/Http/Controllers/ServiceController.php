<?php

namespace App\Http\Controllers;

use App\Models\service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function createService(Request $request){
        if (auth()->check()){   
            $incomingFields = $request->validate([
                'name' => 'required',
                'desc' => 'required',
                'image' => 'required'
            ]);

            if($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('services', 'public');
                $incomingFields['image'] = $imagePath;
            }

            $incomingFields['name'] = strip_tags($incomingFields['name']);
            $incomingFields['desc'] = strip_tags($incomingFields['desc']);
            $incomingFields['user_id'] = auth()->id();
            service::create($incomingFields);
            return Redirect("/dashboard");
        }
        
        return redirect('/');
    }

    public function showEditScreen(service $srvc){
        if (auth()->id() !== $srvc['user_id']){
            return redirect('/');
        }
        
        return view('edit-service', ['srvc' => $srvc]);
    }

    public function updateService(service $srvc, Request $request){
        if (auth()->user()->id == $srvc['user_id']){
            $incomingFields = $request->validate([
                'name' => 'required',
                'desc' => 'required'
            ]);

            if($request->hasFile('image')) {
                service::disk('public')->delete($srvc->image);
                $imagePath = $request->file('image')->store('services', 'public');
                $incomingFields['image'] = $imagePath;
            }

            $incomingFields['name'] = strip_tags($incomingFields['name']);
            $incomingFields['desc'] = strip_tags($incomingFields['desc']);

            $srvc->update($incomingFields);
            
            return redirect('/dashboard');
        }
        
        return redirect('/');
    }

    public function deleteService(service $srvc){
        if (auth()->id() == $srvc['user_id']){
            $srvc->delete();
            service::disk('public')->delete($srvc->image);
        }
        
        return redirect('/dashboard');
    }
}
