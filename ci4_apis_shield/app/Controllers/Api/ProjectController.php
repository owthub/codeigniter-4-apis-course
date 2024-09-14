<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProjectController extends ResourceController
{
    protected $modelName = "App\Models\ProjectModel";
    protected $format = "json";

    // Add Project [POST] -> user_id, project_name, project_budget, description
    public function addProject(){

        $validationRules = [
            "project_name" => "required",
            "project_budget" => "required",
            "description" => "required"
        ];

        if(!$this->validate($validationRules)){

            return $this->respond([
                "status" => false,
                "message" => "Project inputs are required",
                "errors" => $this->validator->getErrors()
            ]);
        }

        $userId = auth()->user()->id;

        if($this->model->insert([
            "user_id" => $userId,
            "project_name" => $this->request->getVar("project_name"),
            "project_budget" => $this->request->getVar("project_budget"),
            "description" => $this->request->getVar("description")
        ])){

            // Success block
            return $this->respond([
                "status" => true,
                "message" => "Successfully, project has been created!"
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Failed to create project for this user."
            ]);
        }
    }

    // List All Projects User Wise
    public function getProjects(){
        
        $userId = auth()->user()->id;

        $projects = $this->model->where("user_id", $userId)->findAll();

        if($projects){

            return $this->respond([
                "status" => true,
                "message" => "Projects found",
                "data" => $projects
            ]);
        } else {

            return $this->respond([
                "status" => false,
                "message" => "No projects found"
            ]);
        }
    }
}
