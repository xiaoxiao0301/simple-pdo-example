<?php


namespace App\lib;


class Helper
{
    /**
     * 获取客户端IP
     * @return array|false|mixed|string
     */
    public static function getClientIp()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        }elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        }elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }else
            $ip = "unknown";

        return $ip;
    }

    /**
     * 过滤客户端输入的内容，防止xss
     * @param $value
     * @return string
     */
    public static function cleanInputData($value)
    {
        return htmlspecialchars($value, ENT_QUOTES);
    }

    /**
     * 用户身份验证
     * @param $uid
     * @param $token
     * @return bool
     */
    public static function checkUserToken($uid, $token)
    {
        if($uid<1 || $token==''){
            return false;
        }
        $tokenRedisKey="user_token_" . $uid;
        $userCheckToken = RedisClient::getInstance()->get($tokenRedisKey);
        if ($userCheckToken && $userCheckToken == $token) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 检查用户的请求签名
     * @param $data
     * @param $sign
     * @return bool
     */
    public static function checkRequestSign($data, $sign)
    {
        if ($sign == '') {
            return false;
        }
        $authConfig = new Config();
        $key = $authConfig['auth.sign'];
        $str = '';
        ksort($data);
        foreach ($data as $k => $v) {
            $str .= $k . '=' .$v .'&';
        }
        $str .= $key;
        $checkSign = md5($str);

        if ($sign == $checkSign) {
            return true;
        }
        return false;
    }
}