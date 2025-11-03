<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class contact extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'no_telp',
        'alamat',
        'link_gmaps',
        'email',
        'url_instagram',
        'url_facebook',
        'url_threads',
        'url_tiktok',
        'url_youtube',
        'url_twitter',
        'user_id'
    ];
}
