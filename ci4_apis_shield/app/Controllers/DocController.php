<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use OpenApi\Generator;

class DocController extends BaseController
{
    public function convertAnnotationToJson()
    {
        $swagger = Generator::scan([ APPPATH . "Controllers" ]);

        $jsonContent = $swagger->toJson();

        $filePath = FCPATH . "swagger.json";

        file_put_contents($filePath, $jsonContent);

        return $this->response->download($filePath, null);
    }
}
