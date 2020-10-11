<?php


namespace App\lib;


class StringCodeMsg
{
    const SUCCESS_CODE = 1;
    const ERR_CODE = 0;

    const SUCCESS_MSG = "请求成功";
    const ERR_MSG = "请求失败";
    const QUERY_STRING_ERROR = "请求参数错误";
    const REQUEST_METHOD_ERROR = "非法请求";
    const REQUEST_SIGN_ERROR = "签名错误";
    const USER_TOKEN_ERROR = "令牌错误";
}