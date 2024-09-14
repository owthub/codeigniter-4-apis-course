<?php

namespace App\Controllers\Api;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use App\Models\CustomUserModel;
use Exception;

/**
 * @OA\Info(
 *   title="CodeIgniter v4 APIs Docs with Shield Auth",
 *   version="1.0.0",
 *   description="This documentation will give idea of Register, Login, Profile and Logout APIs"
 * )
 * 
 * @OA\Server(
 *   url="http://localhost:8080/",
 *   description="This is the information of local development server"
 * )
 */

class AuthController extends ResourceController
{
    //protected $modelName = "App\Models\CustomUserModel";
    protected $format = "json";

    // User Register Method [POST] -> username, email, name, password, gender, phone_number
    /**
     * @OA\Post(
     *    path="/api/register",
     *    summary="User Registration API",
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="username", type="string", example="sanjay_kumar"),
     *         @OA\Property(property="email", type="string", example="sanjay@gmailtest.com"),
     *         @OA\Property(property="name", type="string", example="Sanjay Kumar"),
     *         @OA\Property(property="password", type="string", format="password", example="123456"),
     *         @OA\Property(property="gender", type="string", example="male"),
     *         @OA\Property(property="phone_no", type="string", example="1234567890")
     *      )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="User Registered Successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="User Registered Successfully")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Invalid Input",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="Registration Failed due to invalid entries"),
     *          @OA\Property(property="errors", type="object", additionalProperties={"type":"string"})
     *       )
     *    )
     * )
     */

    public function register(){

        // Request Parameters
        $validationRules = [
            "username" => "required",
            "email" => "required",
            "name" => "required",
            "password" => "required",
            "gender" => "required"
        ];

        if(!$this->validate($validationRules)){

            return $this->respond([
                "status" => false,
                "message" => "Registration Failed due to invalid entries",
                "errors" => $this->validator->getErrors()
            ]);
        }

        $modelObject = new CustomUserModel();

        $entityObject = new User([
            "name" => $this->request->getVar("name"),
            "email" => $this->request->getVar("email"),
            "username" => $this->request->getVar("username"),
            "password" => $this->request->getVar("password"),
            "gender" => $this->request->getVar("gender"),
            "phone_no" => $this->request->getVar("phone_no")
        ]);

        if($modelObject->save($entityObject)){

            return $this->respond([
                "status" => true,
                "message" => "User Registered Successfully",
            ]);
        } else{

            return $this->respond([
                "status" => false,
                "message" => "Failed to Create User",
            ]);
        }
        
    }

    // User Login API [POST] -> email, password
    /**
     * @OA\Post(
     *    path="/api/login",
     *    summary="User Login API",
     *    @OA\RequestBody(
     *      required=true,
     *      @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="email", type="string", example="sanjay@gmailtest.com"),
     *         @OA\Property(property="password", type="string", format="password", example="123456")
     *      )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="User Logged In Successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="User logged in"),
     *          @OA\Property(property="token", type="string", example="your_generated_token_here")
     *       )
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Invalid Login Credentials",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="Login Failed")
     *       )
     *    ),
     *    @OA\Response(
     *       response=500,
     *       description="Internal Server Error",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="An error occurred")
     *       )
     *    )
     * )
     */
    public function login(){

        $validationRules = [
            "email" => "required",
            "password" => "required"
        ];

        if(!$this->validate($validationRules)){

            return $this->respond([
                "status" => false,
                "message" => "Login Failed",
                "errors" => $this->validator->getErrors()
            ]);
        }

        // Check User Details
        $credentials = [
            "email" => $this->request->getVar("email"),
            "password" => $this->request->getVar("password")
        ];

        try{

            if(auth()->loggedIn()){

                auth()->logout();
            }

            $loginAttempt = auth()->attempt($credentials);

            if(!$loginAttempt->isOK()){

                return $this->respond([
                    "status" => false,
                    "message" => "Login Failed"
                ]);
            } else{

                $userId = auth()->user()->id;

                $shieldModelObject = new UserModel;

                $userInfo = $shieldModelObject->findById($userId);

                $tokenInfo = $userInfo->generateAccessToken("12345678sfgfdgffd");

                $raw_token = $tokenInfo->raw_token;

                return $this->respond([
                    "status" => true,
                    "message" => "User logged in",
                    "token" => $raw_token
                ]);
            }
        } catch (Exception $ex){

            return $this->respond([
                "status" => false,
                "message" => $ex->getMessage()
            ]);
        }
    }

    // User Profile MEthod [GET] -> Protected API Method -> Auth Token Value
    /**
     * @OA\Get(
     *    path="/api/profile",
     *    summary="Get User Profile",
     *    @OA\Response(
     *       response=200,
     *       description="User Profile Information",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="Profile information"),
     *          @OA\Property(property="data", type="object", additionalProperties={"type":"string"}) 
     *       )
     *    ),
     *    @OA\Response(
     *       response=401,
     *       description="Unauthorized - Invalid or Missing Token",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="Unauthorized")
     *       )
     *    )
     * )
     */
    public function profile(){

        $userData = auth("tokens")->user();

        return $this->respond([
            "status" => true,
            "message" => "Profile information",
            "data" => $userData
        ]);
    }

    // Logout Method (GET) -> Protected API Method -> Auth Token Value
    /**
     * @OA\Get(
     *    path="/api/logout",
     *    summary="Logout User",
     *    @OA\Response(
     *       response=200,
     *       description="User Logged Out Successfully",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=true),
     *          @OA\Property(property="message", type="string", example="User logged out")
     *       )
     *    ),
     *    @OA\Response(
     *       response=401,
     *       description="Unauthorized - Invalid or Missing Token",
     *       @OA\JsonContent(
     *          @OA\Property(property="status", type="boolean", example=false),
     *          @OA\Property(property="message", type="string", example="Unauthorized")
     *       )
     *    )
     * )
     */
    public function logout(){

        auth()->logout();

        auth()->user()->revokeAllAccessTokens();

        return $this->respond([
            "status" => true,
            "message" => "User logged out"
        ]);
    }
}
