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

class CheckLogControllerApi extends Controller
{
    public function __construct(){
        
    }

    public function checkin(CheckinAndOutRequest $request) {
        $post = $request->only('project_id', 'staff_id');
        $staff = Staff::findOrFail($post['staff_id']);
        $staff->checkLogs()->create([
            'project_id' => $post['project_id'],
            'checkin_time' => now(),
        ]);
        return response()->json([
            'message' => ['checkin công việc thành công']
        ], Response::HTTP_OK);
    }

    public function checkout(CheckinAndOutRequest $request) {
        $post = $request->only('project_id', 'staff_id');
        $staff = Staff::findOrFail($post['staff_id']);
        $log = $staff->checkLogs->last();
        if($log) {
            $log->checkout_time = now();
            $log->save();
            return response()->json([
                'message' => ['checkout công việc thành công']
            ], Response::HTTP_OK);
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
                ->whereDate('checkin_time', now());
        }]
        )->findOrFail($post['project_id']);

        foreach($project->staff as $staff) {
            $log = $staff->checkLogs->last();

            $staff->checkin_time  = $log?->checkin_time;
            $staff->checkout_time = $log?->checkout_time;
            $staff->role = $staff->pivot->role;
            $totalMinutes = $staff->checkLogs()
                ->whereNotNull('checkout_time')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)) as total')
                ->value('total');
            $totalMinutesToday = $staff->checkLogs()
                ->whereDate('checkin_time', today())
                ->whereNotNull('checkout_time')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)) as total')
                ->value('total');

            $staff->total_hours_today = $totalMinutesToday;
            $staff->total_hours = round($totalMinutes / 60);

            $staff->unsetRelation('checkLogs');
        }
        
        return response()->json(
            $project
        );
    }
    
}
