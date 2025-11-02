<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class statistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_pengalaman',
        'proyek_selesai',
        'klien_puas',
        'sebaran_kota',
        'user_id'
    ];
}
