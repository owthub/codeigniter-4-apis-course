<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

class BasicAuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $Authorization = $request->getServer("HTTP_AUTHORIZATION");

        // Check Auth Header
        if(!$Authorization){

            return Services::response()->setStatusCode(400)->setJSON([
                "status" => false,
                "message" => "UnAuthorized Access"
            ]);
        }

        // Verify Authorization Header Pattern [ Basic username:password ]
        $AuthorizationStringParts = explode(" ", $Authorization); //["Basic", "username:password"]

        if(count($AuthorizationStringParts) !== 2 || $AuthorizationStringParts[0] !== "Basic"){
            return Services::response()->setStatusCode(400)->setJSON([
                "status" => false,
                "message" => "UnAuthorized Access"
            ]);
        }

        //Verify Username and password
        list($username, $password) = explode(":", base64_decode($AuthorizationStringParts[1]));

        // Username: CI4_APIS_ADMIN
        // Password: admin#123

        if($username !== "CI4_APIS_ADMIN" || $password !== "admin#123"){
            return Services::response()->setStatusCode(400)->setJSON([
                "status" => false,
                "message" => "UnAuthorized Access"
            ]);
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
