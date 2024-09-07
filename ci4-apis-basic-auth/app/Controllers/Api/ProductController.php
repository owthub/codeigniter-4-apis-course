<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProductController extends ResourceController
{
    protected $modelName = "App\Models\ProductModel";
    protected $format = "json";

    // [POST] - title, cost, description, product_image
    public function addProduct(){
       
        $validationRules = [
            "title" => [
                "rules" => "required|min_length[3]",
                "errors" => [
                    "required" => "Product title is required",
                    "min_length" => "Title must be greater than 3 characters"
                ]
            ],
            "cost" => [
                "rules" => "required|integer|greater_than[0]",
                "errors" => [
                    "required" => "Please provide product cost",
                    "integer" => "Cost must be an integer value",
                    "greater_than" => "Product cost must be greater than 0 value"
                ]
            ]
        ];

        if(!$this->validate($validationRules)){

            return $this->fail($this->validator->getErrors());
        }

        $imageFile = $this->request->getFile("product_image");

        $productImageURL = "";

        if($imageFile){ // abc.png

            // File is available
            $newProductImageName = $imageFile->getRandomName();

            $imageFile->move(FCPATH . "uploads", $newProductImageName);

            $productImageURL = "uploads/" . $newProductImageName;
        }

        $data = $this->request->getPost();

        $title = $data['title'];
        $cost = $data['cost'];
        $description = isset($data['description']) ? $data['description'] : "";

        if($this->model->insert([
            "title" => $title,
            "cost" => $cost,
            "description" => $description,
            "product_image" => $productImageURL
        ])){

            return $this->respond([
                "status" => true,
                "message" => "Product added successfully"
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Failed to add product"
            ]);
        }
    }

    // [GET] 
    public function listAllProducts(){

        $products = $this->model->findAll();

        return $this->respond([
            "status" => true,
            "message" => "Products Found",
            "products" => $products
        ]);
    }

    // [GET] - {product_id}
    public function getSingleProduct($product_id){

        $product = $this->model->find($product_id);

        if($product){

            return $this->respond([
                "status" => true,
                "message" => "Product Found",
                "product" => $product
            ]);
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Product not Found"
            ]);
        }
    }

    // [PUT] - Update Product Data - {product_id}
    public function updateProduct($product_id){

        $product = $this->model->find($product_id);

        if($product){

            // Product Exists
            //$updated_data = json_decode(file_get_contents("php://input"), true);

            $updated_data['title'] = $this->request->getVar("title");
            $updated_data['cost'] = $this->request->getVar("cost");
            $updated_data['description'] = $this->request->getVar("description");

            $product_title = isset($updated_data['title']) ? $updated_data['title'] : $product['title'];
            $product_cost = isset($updated_data['cost']) ? $updated_data['cost'] : $product['title'];
            $product_description = isset($updated_data['description']) ? $updated_data['description'] : $product['description'];

            $productImageObject = $this->request->getFile("product_image");

            $productImageURL = $product['product_image'];

            if($productImageObject){

                $newProductImageName = $productImageObject->getRandomName();
                $productImageObject->move(FCPATH . "uploads", $newProductImageName);

                $productImageURL = "uploads/" . $newProductImageName;
            }

            if($this->model->update($product_id, [
                "title" => $product_title,
                "cost" => $product_cost,
                "description" => $product_description,
                "product_image" => $productImageURL
            ])){

                return $this->respond([
                    "status" => true,
                    "message" => "Product has been updated"
                ]);
            }else{

                return $this->respond([
                    "status" => false,
                    "message" => "Failed to update product"
                ]);
            }
        }else{
            return $this->respond([
                "status" => false,
                "message" => "Product not found"
            ]);
        }
    }

    // [DELETE] -- Delete Product Data - {product_id}
    public function deleteProduct($product_id){
        
        $product = $this->model->find($product_id);

        if($product){

            // Need to delete product
            if($this->model->delete($product_id)){

                return $this->respond([
                    "status" => true,
                    "message" => "Successfully product has been deleted"
                ]);
            }else{

                return $this->respond([
                    "status" => false,
                    "message" => "Failed to delete product"
                ]);
            }
        }else{

            return $this->respond([
                "status" => false,
                "message" => "Product not found"
            ]);
        }
    }
}
