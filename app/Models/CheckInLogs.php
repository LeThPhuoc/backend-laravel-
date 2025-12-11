<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class CheckInLogs extends Model
{
    //
    use HasFactory;

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

}
