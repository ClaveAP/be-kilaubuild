<?php

namespace App\Http\Controllers;

use App\Models\contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // GET: Ambil Semua Data
    public function index(){
        $cont = contact::all();

        return response()->json([
            'success' => true,
            'data' => $cont
        ], 200);
    }

    // POST: Simpan Data Baru
    public function store(Request $request){
        // Tidak perlu cek auth()->check() manual karena sudah dihandle middleware route

        $incomingFields = $request->validate([
            'no_telp' => 'required',
            'alamat' => 'required',
            'link_gmaps' => 'required',
            'email' => 'required'
        ]);

        $incomingFields['no_telp'] = strip_tags($incomingFields['no_telp']);
        $incomingFields['alamat'] = strip_tags($incomingFields['alamat']);
        $incomingFields['link_gmaps'] = strip_tags($incomingFields['link_gmaps']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);

        // Otomatis ambil ID user yang sedang login
        $incomingFields['user_id'] = auth()->id();

        $cont = contact::create($incomingFields);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil dibuat',
            'data' => $cont
        ], 201);
    }

    // PUT: Update Data
    public function update(contact $cont, Request $request){

        $incomingFields = $request->validate([
            'no_telp' => 'required',
            'alamat' => 'required',
            'link_gmaps' => 'required',
            'email' => 'required',
        ]);

        $incomingFields['no_telp'] = strip_tags($incomingFields['no_telp']);
        $incomingFields['alamat'] = strip_tags($incomingFields['alamat']);
        $incomingFields['link_gmaps'] = strip_tags($incomingFields['link_gmaps']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);

        $cont->update($incomingFields);

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil diperbarui',
            'data' => $cont
        ], 200);
    }

    // DELETE: Hapus Data
    public function destroy(contact $cont){
        $cont->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kontak berhasil dihapus',
            'data' => null
        ], 200);
    }
}
