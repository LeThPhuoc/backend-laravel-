<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_date', 'end_date', 'address'];

    public function boss()
    {
        return $this->belongsToMany(Boss::class, 'boss_project', 'project_id', 'boss_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'staff_project', 'project_id', 'staff_id')
                    ->withPivot('salary', 'role')
                    ->withTimestamps();
    }

    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];
}
