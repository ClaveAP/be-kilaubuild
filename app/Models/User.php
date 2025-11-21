<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Tabel Lain
    public function adminPost(){
        return $this->hasMany(InstagramPost::class, 'user_id');
    }

    public function adminFaqs() {
        return $this->hasMany(Faq::class, 'user_id');
    }

    public function adminPD() {
        return $this->hasMany(projectDone::class, 'user_id');
    }

    public function adminOP() {
        return $this->hasMany(ongoingProjects::class, 'user_id');
    }

    public function adminDI() {
        return $this->hasMany(desainInterior::class, 'user_id');
    }

    public function adminTstmn() {
        return $this->hasMany(testimoni::class, 'user_id');
    }

    public function adminStatistic() {
        return $this->hasMany(statistic::class, 'user_id');
    }

    public function adminContact() {
        return $this->hasMany(contact::class, 'user_id');
    }
    
}