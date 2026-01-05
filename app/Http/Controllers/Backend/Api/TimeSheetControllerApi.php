<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CheckLog\CheckinAndOutRequest;
use App\Models\Boss;
use App\Models\Staff;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TimeSheetControllerApi extends Controller
{
    public function __construct(){
        
    }
    
    public function getListStaffInProject(Request $request) {
        $post = $request->only('project_id', 'staff_id', 'boss_id');
        $staff = Project::with('staff')->findOrFail($post['project_id']);
        foreach($staff->staff as $value) {
            $value->timesheet;
        }
        return response()->json($staff);
    }
    
}
