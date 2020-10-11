<?php


namespace App\service;

use App\lib\DbClient;
use App\lib\RedisClient;
use App\lib\Response;

class Post
{
    /**
     * 发表帖子
     * @param array $data
     * @return bool
     */
    public function add(array $data)
    {
        // sql语句中不需要添加双引号
        $sql = <<<SQL
    insert into `board`(subject, author, idate, replies, body, ip) values(:subject, :author, :idate, :replies, :body, :ip);
SQL;
        $postId = DbClient::getInstance()->insertGetId($sql, $data);
        if ($postId) {
            $this->postCache($postId);
            return true;
        } else {
            return false;
        }

    }

    /**
     * 获取帖子列表
     * @return array
     */
    public  function lists($p = 1)
    {
        $postRedisKey = "user_publish_post";
        $posts = RedisClient::getInstance()->zRevRange($postRedisKey, 0, -1, true);
        $formatPosts = [];
        foreach ($posts as $member => $score) {
            $formatPosts[] = $member;
        }
        $length = 50;
        $start = ($p - 1) * $length;
        $postIds = array_slice($formatPosts, $start, $length);
        if ($postIds) {
            $resultData = [];
            $sql = <<<SQL
        select * from `board` where id=:id
SQL;
            foreach ($postIds as $id) {
                $queryData = DbClient::getInstance()->query($sql, [":id" => $id]);
                if ($queryData !== false) {
                    $resultData[] = $queryData;
                }
            }
            return $resultData;
        } else {
            return $postIds;
        }
    }

    /**
     * 将所有已发表的帖子的id存储起来
     * @param $id
     */
    protected function postCache($id)
    {
        $postRedisKey = "user_publish_post";
        RedisClient::getInstance()->zAdd($postRedisKey, time(), $id);
    }

}
