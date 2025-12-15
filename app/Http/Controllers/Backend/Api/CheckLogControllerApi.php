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

    public function checkout(Request $request) {
        $post = $request->only('project_id', 'staff_id');
        $staff = Staff::findOrFail($post['staff_id']);
        $log = $staff->checkLogs()
            ->whereDate('checkin_time', now())
            ->first();
        if($log) {
            $log->checkout_time = now();
            $log->save();
        } else {
            return response()->json([
                'message' => 'No check-in record found for today.'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getDetail(Request $request) {
        $post = $request->only('project_id');
        $project = Project::with(
            ['staff.checkLogs' => function ($query) {
            $query
                ->whereDate('checkin_time', now())
                ->first();
        }]
        )->findOrFail($post['project_id']);

        foreach($project->staff as $staff) {
            $log = $staff->checkLogs->first();

            $staff->checkin_time  = $log?->checkin_time;
            $staff->checkout_time = $log?->checkout_time;

            $staff->unsetRelation('checkLogs');
        }
        // dd($project);
        return response()->json(
            $project
        );
    }
    
}
