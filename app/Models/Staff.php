<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => 'staff'];
    }

    protected $table = 'staff';
    protected $fillable = ['name','tel','address','login_name','email','password',];

    protected $hidden = ['password'];

    public function bosses()
    {
        return $this->belongsToMany(Boss::class, 'boss_staff')
                    ->withPivot(['salary'])
                    ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_staff', 'staff_id', 'project_id')
                    ->withPivot('salary', 'role')
                    ->withTimestamps();
    }
}
