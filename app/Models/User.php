<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adminPost(){
        $this->hasMany(InstagramPost::class, 'user_id');
    }

    public function adminServices() {
        $this->hasMany(service::class, 'user_id');
    }

    public function adminFaqs() {
        $this->hasMany(Faq::class, 'user_id');
    }

    public function adminPD() {
        $this->hasMany(projectDone::class, 'user_id');
    }

    public function adminOP() {
        $this->hasMany(ongoingProjects::class, 'user_id');
    }

    public function adminDI() {
        $this->hasMany(desainInterior::class, 'user_id');
    }

    public function adminTstmn() {
        $this->hasMany(testimony::class, 'user_id');
    }

    public function adminStatistic() {
        $this->hasMany(statistic::class, 'user_id');
    }

    public function adminContact() {
        $this->hasMany(contact::class, 'user_id');
    }
}
