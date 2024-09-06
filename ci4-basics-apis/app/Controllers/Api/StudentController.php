<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\StudentModel;

class StudentController extends ResourceController
{
    protected $modelName = "App\Models\StudentModel";
    protected $format = "xml"; //xml

    // Add Student - POST
    public function addStudent(){
        
        $data = $this->request->getPost();

        $name = isset($data['name']) ? $data['name'] : "";
        $email = isset($data['email']) ? $data['email'] : "";
        $phone_no = isset($data['phone_no']) ? $data['phone_no'] : "";

        if(empty($name) || empty($email)){

            // Display Errors
            return $this->respond([
                "status" => false,
                "message" => "Please provide the required fields (name, email)"
            ]);
        }

        $student_data = $this->checkStudentByEmail($email);

        if(!empty($student_data)){

            return $this->respond([
                "status" => false,
                "message" => "Email Already Exists"
            ]);
        }else{
            // Add Student to Table
            if($this->model->insert([
                "name" => $name,
                "email" => $email,
                "phone_no" => $phone_no
            ])){

                // Success Block
                return $this->respond([
                    "status" => true,
                    "message" => "Successfully, Student has been created"
                ]);
            } else{

                // Failed Block
                return $this->respond([
                    "status" => false,
                    "message" => "Failed to add student"
                ]);
            }
        }
    }

    // List Students - GET
    public function listStudents(){

        $students = $this->model->findAll();

        return $this->respond([
            "status" => true,
            "message" => "Students data",
            "data" => $students
        ]);
    }

    // Show Single Student Data - GET
    public function getSingleStudentData($student_id){

        $student_data = $this->model->find($student_id);

        if(!empty($student_data)){

            return $this->respond([
                "status" => true,
                "message" => "Student data",
                "data" => $student_data
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Student doesn't exists with this ID"
            ]);
        }
    }

    // Update Student Data - PUT
    public function updateStudent($student_id){

        $student = $this->model->find($student_id);

        if(!empty($student)){

            //$data = $this->request->getVar();
            $data = json_decode(file_get_contents("php://input"), true);

            $updated_data = [
                "name" => isset($data['name']) && !empty($data['name']) ? $data['name'] : $student['name'],
                "email" => isset($data['email']) && !empty($data['email']) ? $data['email'] : $student['email'],
                "phone_no" => isset($data['phone_no']) && !empty($data['phone_no']) ? $data['phone_no'] : $student['phone_no']
            ];

            if($this->model->update($student_id, $updated_data)){

                return $this->respond([
                    "status" => true,
                    "message" => "Student data updated successfully"
                ]);
            }else{

                return $this->respond([
                    "status" => false,
                    "message" => "Failed to update data"
                ]);
            }

        }else{

            return $this->respond([
                "status" => false,
                "message" => "Student doesn't exists with this ID"
            ]);
        }
    }

    // Delete Student Data - DELETE
    public function deleteStudent($student_id){

        $student = $this->model->find($student_id);

        if(!empty($student)){

            if($this->model->delete($student_id)){

                return $this->respond([
                    "status" => true,
                    "message" => "Student deleted successfully"
                ]);
            }else{

                return $this->respond([
                    "status" => false,
                    "message" => "Failed to delete data"
                ]);
            }
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Student doesn't exists with this ID"
            ]);
        }
    }

    // Check Student via Email Address
    private function checkStudentByEmail($email){

        return $this->model->where("email", $email)->first();
    }
}
