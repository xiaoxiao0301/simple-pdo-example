<?php
error_reporting(E_ALL);
require_once "vendor/autoload.php";

use App\controller\PostController;

$clientServer = $_SERVER;
$pathInfo = $clientServer['PATH_INFO'];
$postController = new PostController($clientServer);
if ($pathInfo == "/add") {
    $postData = $_POST;
    $postController->add($postData);
} else if ($pathInfo == "/list") {
    $getData = $_GET;
    $postController->list($getData);
} else {
    echo "非法请求";
    exit(-1);
}

