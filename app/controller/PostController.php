<?php


namespace App\controller;

use App\lib\Helper;
use App\lib\Response;
use App\lib\Request;
use App\lib\StringCodeMsg;
use App\service\Post as PostService;

class PostController
{
    /**
     * @var PostService
     */
    public $postService;

    /**
     * @var Request
     */
    public $requestService;

    /**
     * PostController constructor.
     * @param $server
     */
    public function __construct($server)
    {
        $this->postService = new PostService();
        $this->requestService = new Request($server);
    }

    public function add($data)
    {
        if ($this->requestService->isPost()) {
            $uid = $data['uid'];
            $token = $data['token'];
            $sign = $data['sign'];
            $subject = Helper::cleanInputData($data['subject']);
            $author = Helper::cleanInputData($data['author']);
            $body = Helper::cleanInputData($data['body']);

            // 参数校验
            if ($uid == "" || $token == "" || $subject == "" || $author == "" || $body == "") {
                Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::QUERY_STRING_ERROR);
            }

            // 令牌校验
            $checkUserToken = Helper::checkUserToken($uid, $token);
            if (!$checkUserToken) {
                Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::USER_TOKEN_ERROR);
            }

            // 签名校验
            $checkUserSign = Helper::checkRequestSign([
                "uid" => $uid,
                "token" => $token,
                "subject" => $subject,
                "author" => $author,
                "body" => $body,
            ], $sign);

            if (!$checkUserSign) {
                Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::REQUEST_SIGN_ERROR);
            }

            $insertData = [
                ":subject" => $subject,
                ":author" => $author,
                ":idate" => date("Y-m-d H:i:s", time()),
                ":replies" => 0,
                ":body" => $body,
                ":ip" => Helper::getClientIp(),
            ];

            $result = $this->postService->add($insertData);
            if ($result) {
                Response::json(StringCodeMsg::SUCCESS_CODE, StringCodeMsg::SUCCESS_MSG);
            } else {
                Response::json(StringCodeMsg::ERR_CODE, StringCodeMsg::ERR_MSG);
            }


        } else {
            Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::REQUEST_METHOD_ERROR);
        }
    }

    public function list($data)
    {
        if ($this->requestService->isGet()) {
            $uid = $data['uid'];
            $token = $data['token'];
            $sign = $data['sign'];
            $p = $data['p'];

            // 参数校验
            if ($uid == "" || $token == "" || $sign == "" || $p < 1) {
               Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::QUERY_STRING_ERROR);
            }

            // 令牌校验
            $checkUserToken = Helper::checkUserToken($uid, $token);
            if (!$checkUserToken) {
                Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::USER_TOKEN_ERROR);
            }

            // 签名校验
            $checkUserSign = Helper::checkRequestSign([
                "uid" => $uid,
                "token" => $token
            ], $sign);

            if (!$checkUserSign) {
                Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::REQUEST_SIGN_ERROR);
            }

            $result = $this->postService->lists($p);
            Response::json(StringCodeMsg::SUCCESS_CODE, StringCodeMsg::SUCCESS_MSG, $result);
        } else {
            Response::json(StringCodeMsg::ERR_CODE,StringCodeMsg::REQUEST_METHOD_ERROR);
        }



    }
}