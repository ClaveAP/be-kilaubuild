<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Mengompres gambar dan menyimpannya kembali ke path yang sama.
     * Menggunakan fungsi native PHP GD agar tidak perlu library tambahan.
     */
    public static function compressImage($path, $quality = 75)
    {
        // Dapatkan full path dari storage public
        $fullPath = Storage::disk('public')->path($path);
        
        // Cek apakah file ada
        if (!file_exists($fullPath)) {
            return false;
        }

        $info = getimagesize($fullPath);
        $mime = $info['mime'];

        // Buat image resource berdasarkan tipe file
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($fullPath);
                // Konversi palette ke true color agar bisa dikompres jpg/png
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($fullPath);
                break;
            default:
                return false; // Format tidak didukung
        }

        // Simpan kembali file (Overwrite) dengan kompresi
        // Jika PNG, quality 0-9 (dikali 10 kira2 konversi ke skala 0-100 jpeg)
        if ($mime == 'image/png') {
            // PNG compression level 0 (no compression) to 9.
            // Kita pakai skala quality terbalik 75 -> ~2
            $pngQuality = 9 - round(($quality / 100) * 9);
            imagepng($image, $fullPath, $pngQuality); 
        } else {
            // JPEG quality 0-100
            imagejpeg($image, $fullPath, $quality);
        }

        // Bersihkan memori
        imagedestroy($image);

        return true;
    }
}