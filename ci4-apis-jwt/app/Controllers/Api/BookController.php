<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class BookController extends ResourceController
{
    protected $modelName = "App\Models\Phase3\BookModel";
    protected $format = "json";

    // Create Book
    // [POST] -> Protected MEthod -> Valid Token value before accessing it
    // author_id, name, publication, cost
    public function createBook(){

        $validationRules = [
            "name" => [
                "rules" => "required"
            ],
            "cost" => [
                "rules" => "required"
            ]
        ];

        if(!$this->validate($validationRules)){

            return $this->respond([
                "status" => false,
                "message" => "Please provide the required fields",
                "errors" => $this->validator->getErrors()
            ]);
        }

        $tokenInformation = $this->request->userData;
        $userId = $tokenInformation['user']->id;

        $bookData = [
            "author_id" => $userId,
            "name" => $this->request->getVar("name"),
            "publication" => $this->request->getVar("publication"),
            "cost" => $this->request->getVar("cost")
        ];

        if($this->model->save($bookData)){

            // Success 
            return $this->respond([
                "status" => true,
                "message" => "Book created successfully"
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Failed to create book"
            ]);
        }
    }

    // List Books
    // [GET] -> Protected MEthod -> Valid Token value before accessing it
    public function authorBooks(){

        $tokenInformation = $this->request->userData;
        $userId = $tokenInformation['user']->id;

        $books = $this->model->where("author_id", $userId)->findAll();

        if($books){

            return $this->respond([
                "status" => true,
                "message" => "Books Found",
                "books" => $books
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "No books found for this author"
            ]);
        }
    }

    // Delete a Book
    // [DELETE] -> Protected MEthod -> Valid Token value before accessing it
    public function deleteAuthorBook($book_id){
        
        $tokenInformation = $this->request->userData;
        $author_id = $tokenInformation['user']->id;

        $book = $this->model->where(array(
            "id" => $book_id,
            "author_id" => $author_id
        ))->first();

        if($book){

            // Book Exists
            if($this->model->delete($book_id)){

                return $this->respond([
                    "status" => true,
                    "message" => "Book has been deleted"
                ]);
            } else{

                return $this->respond([
                    "status" => false,
                    "message" => "Failed to delete book"
                ]);
            }
        } else{
            return $this->respond([
                "status" => false,
                "message" => "Book not found"
            ]);
        }
    }
}
