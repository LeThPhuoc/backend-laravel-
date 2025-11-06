<?php

namespace App\Http\Controllers\Backend\Api\Project;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Project\ProjectStoreRequest;
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
        $post = $request->only('name', 'description', 'start_date', 'end_date');
        $project = Project::create([
            'name' => $post['name'],
            'description' => $post['description'],
            'start_date' => $post['start_date'],
            'end_date' => $post['end_date'],
        ]);
        $project->boss()->attach([
            $bossId
        ]);
    }

    public function addStaff($request){
        
    }

    public function getListProject() {
        
    }
}
