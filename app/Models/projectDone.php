<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class projectDone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'desc',
        'image',
        'year',
        'user_id'
    ];

    public function searchAuthor(){
        return $this->belongsTo(User::class, 'user_id');
    }
}