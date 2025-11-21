<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class testimoni extends Model
{
    use HasFactory;

    protected $table = 'testimonies'; 

    protected $fillable = [
        'name',
        'review',
        'star',
        'user_id'
    ];

}