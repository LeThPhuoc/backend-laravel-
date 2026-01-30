<?php

namespace App\Http\Controllers\Backend\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CheckLog\CheckinAndOutRequest;
use App\Models\Boss;
use App\Models\Staff;
use App\Models\Project;
use App\Models\TimeSheet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Carbon\Carbon;

class TimeSheetControllerApi extends Controller
{
    public function __construct(){
        
    }
    
    public function getDetailTimeSheet(Request $request, $project_id) {
        $post = $request->only('project_id', 'staff_id', 'boss_id');
        $project = Project::with('staff')->findOrFail($project_id);
        foreach($project->staff as $value) {
            $value->total_timekeeping_number_today = $value->timesheet()->where('created_at', '>=', Carbon::today())->timekeeping_number ?? 0;
            $total = $value->timesheet()
                    ->sum('timekeeping_number');
            $value->total_timekeeping_number = $total;
            $totalMinutes = $value->checkLogs()
                ->whereNotNull('checkout_time')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)) as total')
                ->value('total');
            $totalMinutesToday = $value->checkLogs()
                ->whereDate('checkin_time', today())
                ->whereNotNull('checkout_time')
                ->selectRaw('SUM(TIMESTAMPDIFF(MINUTE, checkin_time, checkout_time)) as total')
                ->value('total');
            $value->total_hours_today = $totalMinutesToday ?? 0;
            $value->total_hours = round($totalMinutes / 60) ?? 0;
            $value->role = $value->pivot->role;
            unset($value->timesheet);
        }
        return response()->json($project);
    }

    public function timesheetUpsert(Request $request) {
        $post = $request->only('timesheet_id', 'timekeeping_number');
        $timesheet = TimeSheet::findOrFail($post['timesheet_id']);
        dd($timesheet);
    }

    public function createTimeSheet(Request $request) {
        $post = $request->only('staff_id', 'project_id', 'timekeeping_number', 'boss_id');
        $timesheet = Staff::findOrFail($post['staff_id']);
        $timesheet->timesheet()->create([
            'project_id' => $post['project_id'],
            'boss_id' => $post['boss_id'],
            'timekeeping_number' => $post['timekeeping_number'],
        ]);
        return response()->json($timesheet);
    }
}
