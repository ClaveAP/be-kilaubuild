<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'no_telp',
        'feedback'
    ];
}
