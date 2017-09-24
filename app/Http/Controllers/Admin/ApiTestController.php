<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use  App\Services\ApiServer\ApiResponse;

class ApiTestController extends ApiController
{

    protected  $api_response = NUll;

    public  function  __construct()
    {
        parent::__construct();
        $this->api_response = new ApiResponse();
    }

    public function anyCreate()
    {

    }


    public  function  ApiTest()
    {
        $data_str = '{
            "bid": "26842",
            "code": "200",
            "data": true,
            "locale": "zh",
            "message": "",
            "requestId": "e98ebf3b-235e-444d-8e53-e9ce1f4921d4",
            "siteId": "cn",
            "successResponse": true
        }';


        $data = json_decode($data_str,true);
        return $this->api_response->withData($data);
        //echo 132412;exit;
    }
}
