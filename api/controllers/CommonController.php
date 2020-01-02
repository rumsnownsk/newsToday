<?php

namespace api\controllers;

abstract class CommonController
{
    public static $requestParams;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
        header("Content-Type: application/json;  charset=UTF-8");
        header("Access-Control-Allow-Headers:Access-Control-Allow-Headers, Access-Control-Allow-Methods, Authorization, X-Requested-With, content-type");

        self::$requestParams = [];

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if ($method == 'put' || $method == 'delete') {
            parse_str(file_get_contents('php://input'), self::$requestParams);
        } elseif ($method == 'get') {
            self::$requestParams = $_GET;
        } elseif ($method == 'post') {
            self::$requestParams = $_POST;
        }

    }

    protected function response($data = [], $status = 500)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            201 => 'Created',
            204 => 'No Content',
            404 => 'Not Found',
            422 => 'Unprocessable Entity',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])
            ? $status[$code]
            : $status[500];
    }

}