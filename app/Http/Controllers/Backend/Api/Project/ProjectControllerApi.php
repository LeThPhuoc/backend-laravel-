<?php

namespace App\Http\Controllers\Backend\Api\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\EditProjectRequest;
use App\Http\Requests\Project\AddStaffProjectRequest;
use App\Http\Requests\Project\DeleteStaffBossFromProjectRequest;
use App\Http\Requests\Project\EditStaffBossProjectRequest;
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

    public function getListProject(Request $request, $role, $id) {
        $search = $request->query('search');
        switch($role) {
            case 'boss': {
                $projects = Boss::with(['projects' => function ($q) use ($search) {
                    if ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    }
                }])->findOrFail($id);
            }
                break;
            case 'staff': {
                $projects = Staff::with(['projects' => function ($q) use ($search) {
                    if ($search) {
                        $q->where('name', 'LIKE', "%$search%");
                    }
                }])->findOrFail($id);
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
        foreach($projects->boss as $val) {
            $val->role = $val->pivot->role;   
        }
        return response()->json(
            $projects
        );
    }

    public function deleteStaffBossFromProject(DeleteStaffBossFromProjectRequest $request, $projectId) {
        $post = $request->only('staff_id', 'boss_id');
        $project =Project::findOrFail($projectId);
        $project->staff()->detach($post['staff_id']);
        $project->boss()->detach($post['boss_id']);
        return response()->json([
            'message' => ['Xóa nhân viên khỏi dự án thành công']
        ], Response::HTTP_OK);
    }

    public function editStaffBossInProject(Request $request, $project_id, $role, $id) {
        $project = Project::findOrFail($project_id);
        if($role == 'staff') {
            $post = $request->only('role', 'salary');
            $project->staff()->updateExistingPivot($id, [
                'role' => $post['role'],
                'salary' => $post['salary']
            ]);
            return response()->json([
                'message' => ['Sửa nhân viên dự án thành công']
            ], Response::HTTP_OK);
        } else if($role == 'boss') {
            $post = $request->only('role');
            $project->boss()->updateExistingPivot($id, [
                'role' => $post['role'],
            ]);
            return response()->json([
                'message' => ['Sửa quản lý dự án thành công']
            ], Response::HTTP_OK);
        }
    }

    public function editProject(EditProjectRequest $request, $project_id) {
        $project = Project::findOrFail($project_id);
        $post = $request->only('name', 'description', 'start_date', 'end_date', 'address');
        $project->update([
            'name' => $post['name'],
            'address' => $post['address'],
            'description' => $post['description'],
            'start_date' => $post['start_date'],
            'end_date' => $post['end_date'],
        ]);
        return response()->json([
            'message' => ['Cập nhật dự án thành công']
        ], Response::HTTP_OK);
    }

    public function deleteProject($project_id) {
        $project = Project::findOrFail($project_id);
        $project->delete();
        return response()->json([
            'message' => ['Xóa dự án thành công']
        ], Response::HTTP_OK);
    }
}
