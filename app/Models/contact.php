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
        'user_id'
    ];
}
