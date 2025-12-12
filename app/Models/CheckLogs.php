<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CheckLogs extends Model
{
    //
    use HasFactory;

    protected $fillable = ['checkin_time', 'checkout_time', 'project_id', 'staff_id'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
