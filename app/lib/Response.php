<?php


namespace App\lib;


class Response
{
    /**
     * 简单接口返回格式
     * @param $code
     * @param $msg
     * @param array $data
     * @return string
     */
    public static function json($code, $msg, $data = [])
    {
        $returnData = [
            "code" => $code,
            "msg" => $msg,
            "data" => $data
        ];

        echo json_encode($returnData);
        exit();
    }
}