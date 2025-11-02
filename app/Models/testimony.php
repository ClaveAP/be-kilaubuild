<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class testimony extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'review',
        'star',
        'user_id'
    ];

}
