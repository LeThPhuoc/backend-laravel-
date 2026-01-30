<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class TimeSheet extends Model
{
    //
    use HasFactory;
    protected $table = 'timesheet';
    protected $fillable = ['project_id','staff_id','timekeeping_number', 'boss_id'];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function boss() {
        return $this->belongsTo(Project::class, 'boss_id');
    }

    public function staff() {
        return $this->belongsTo(Project::class, 'staff_id');
    }
}
