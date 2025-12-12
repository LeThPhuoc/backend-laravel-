<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boss;
use App\Models\Staff;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class CheckLogControllerApi extends Controller
{
    public function __construct(){
        
    }

    public function checkin(Request $request) {
        $post = $request->only('project_id', 'staff_id');
        $staff = Staff::findOrFail($post['staff_id']);
        $staff->checkLogs()->create([
            'project_id' => $post['project_id'],
            'checkin_time' => now(),
        ]);
    }
    
}
