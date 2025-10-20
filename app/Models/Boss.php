<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Boss extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'boss'];
    }

    protected $table = 'boss';
    protected $fillable = ['name','tel','address','login_name','email','password',];

    protected $hidden = ['password'];

    public function staffs()
    {
        return $this->belongsToMany(Staff::class, 'boss_staff')
                    ->withPivot(['salary'])
                    ->withTimestamps();
    }
}
