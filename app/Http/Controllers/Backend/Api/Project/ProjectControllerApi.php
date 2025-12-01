<?php

namespace App\Http\Controllers\Backend\Api\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Project\ProjectStoreRequest;
use App\Http\Requests\Project\EditProjectRequest;
use App\Http\Requests\Project\AddStaffBossProjectRequest;
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

    public function addStaffBoss(AddStaffBossProjectRequest $request, $projectId){
        $project = Project::findOrFail($projectId);
        $post = $request->only('staffs', 'bosses');
        if(count($post['staffs'])) {
            foreach($post['staffs'] as $staff) {
                $project->staff()->attach([
                    $staff['id'] => [
                        'role' => $staff['role'],
                        'salary' => $staff['salary']
                    ]
                    ]);
            }
            return response()->json([
            'message' => ['Thêm nhân viên thành công']
        ], Response::HTTP_OK);
        }
        if(count($post['bosses'])) {
            foreach($post['bosses'] as $staff) {
                $project->boss()->attach([
                $staff['id'] => [
                    'role' => $staff['role'],
                    ]
                ]);
            }
            return response()->json([
            'message' => ['Thêm quản lí thành công']
        ], Response::HTTP_OK);
        }
    }

    public function getListProject(Request $request, $role, $id) {
        $search = $request->query('search');
        switch($role) {
            case 'boss': {
                $boss = Boss::findOrFail($id);
                $projects = $boss->projects()->when($search, function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->paginate(5);
                return response()->json([
                    'data' => $projects->items(),
                    'page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage()
                ]);
            }
                break;
            case 'staff': {
                $staff = Staff::findOrFail($id);
                $projects = $staff->projects()->when($search, function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%");
                })->paginate(5);
                return response()->json([
                    'data' => $projects->items(),
                    'page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage()
                ]);
            }
                break;
        }
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
            'message' => ['Xóa thành viên khỏi dự án thành công']
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

    public function getStaffNotInProject(Request $request, $project_id, $boss_id) {
        $search = $request->input('search');
        $staffs = Staff::whereHas('bosses', function ($q) use ($boss_id) {
            $q->where('boss_id', $boss_id);
        })
        ->whereDoesntHave('projects' , function ($q) use ($project_id) {
            $q->where('projects.id', $project_id);
        })
        ->when($search, function($q) use ($search) {
            $q->where("name", "LIKE", "%$search%");
        })
        ->paginate(20);
        return response()->json(
            $staffs->items()
        , Response::HTTP_OK);
    }

    public function getBossNotInProject(Request $request, $project_id) {
        $search = $request->input('search');
        $bosses = Boss::whereDoesntHave('projects' , function ($q) use ($project_id) {
            $q->where('projects.id', $project_id);
        })
        ->when($search, function($q) use ($search) {
            $q->where("name", "LIKE", "%$search%");
        })
        ->paginate(20);
        return response()->json(
            $bosses->items()
        , Response::HTTP_OK);
    }
}
