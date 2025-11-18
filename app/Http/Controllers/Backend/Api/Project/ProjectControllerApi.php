<?php

namespace App\Http\Controllers\Backend\Api\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\AddStaffProjectRequest;
use App\Http\Requests\Project\DeleteStaffFromProjectRequest;
use App\Models\Boss;
use App\Models\Staff;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class ProjectControllerApi extends Controller
{
    public function __construct(){
        
    }


    public function store(ProjectStoreRequest $request, $bossId){
        $post = $request->only('name', 'description', 'start_date', 'end_date', 'address');
        $project = Project::create([
            'name' => $post['name'],
            'address' => $post['address'],
            'description' => $post['description'],
            'start_date' => $post['start_date'],
            'end_date' => $post['end_date'],
        ]);
        $project->boss()->attach([
            $bossId
        ]);
    }

    public function addStaff(AddStaffProjectRequest $request, $projectId){
        $project = Project::findOrFail($projectId);
        $post = $request->only('staffs');
        foreach($post['staffs'] as $staff) {
            $project->staff()->attach([
                $staff['id'] => [
                    'role' => $staff['role'],
                    'salary' => $staff['salary']
                ]
                ]);
        }
    }

    public function getListProject($role, $id) {
        switch($role) {
            case 'boss': {
                $projects = Boss::with('projects')->findOrFail($id);
            }
                break;
            case 'staff': {
                $projects = Staff::with('projects')->findOrFail($id);
            }
                break;
        }
        
        if(!count($projects->projects)) {
            return response()->json([
                'message' => ['không có dự án nào']
            ]);
        }

        foreach($projects->projects as $val) {
            $val->staff;
            $val->boss;
            foreach($val->staff as $staff) {
                $staff->role = $staff->pivot->role;   
                $staff->salary = $staff->pivot->salary;   
            }
        }

        return response()->json(
            $projects->projects
        );
    }

    public function getProjectDetail($id) {
        $projects = Project::with('boss', 'staff')->findOrFail($id);
        foreach($projects->staff as $val) {
            $val->role = $val->pivot->role;   
            $val->salary = $val->pivot->salary;   
        }
        return response()->json(
            $projects
        );
    }

    public function deleteStaffFromProject(DeleteStaffFromProjectRequest $request, $projectId) {
        $staffId = $request->input('staff_id');
        $project =Project::findOrFail($projectId);
        $project->staff()->detach($staffId);
        return response()->json([
            'message' => ['Xóa nhân viên khỏi dự án thành công']
        ], Response::HTTP_OK);
    }
}
