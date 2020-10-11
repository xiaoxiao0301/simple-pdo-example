<?php

namespace App\lib;

/**
 * 数据库操作基础类
 *
 * Class DbClient
 * @package lib
 */
class DbClient
{
    /**
     * 类对象
     * @var null
     */
    private static $instance = null;
    /**
     * 链接对象
     * @var null
     */
    private static $conn = null;

    /**
     * @var string 数据库类型
     */
    protected $dbms = "mysql";
    /**
     * @var string 服务器地址
     */
    protected $dbhost;
    /**
     * @var string 数据库名称
     */
    protected $database;
    /**
     * @var string 数据库用户
     */
    protected $dbuser;
    /**
     * @var string 数据库用户密码
     */
    protected $dbpass;
    /**
     * @var string 数据库端口
     */
    protected $dbport;
    /**
     * @var string 数据库字符集
     */
    protected $dbchart;
    /**
     * @var string 数据库链接dsn
     */
    protected $dsn;


    private function __construct()
    {
        $this->conn();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    /**
     * 插入数据到数据库中
     * @param $sql
     * @param $data
     * @return bool
     */
    public function add($sql, $data)
    {
        try {
            $stmt = self::$conn->prepare($sql);
            $result = $stmt->execute($data);
        } catch (\PDOException $exception) {
            // 应该是要记录到日志中，而不是直接返回
//            echo "插入数据出错:" . $exception->getMessage();
            $result = false;
        }

        return $result;
    }

    /**
     * 获取插入数据库中的表的ID
     * @param $sql
     * @param $data
     * @return int
     */
   public function insertGetId($sql, $data)
   {
       try {
           $stmt = self::$conn->prepare($sql);
           $stmt->execute($data);
           $id = self::$conn->lastInsertId();
       } catch (\PDOException $exception) {
           // 应该是要记录到日志中，而不是直接返回
//           echo "插入数据出错:" . $exception->getMessage();
           $id = 0;
       }

       return $id;
   }

    /**
     * 获取查询结果
     * @param $sql
     * @param array $params
     * @return array|bool
     */
   public function query($sql, $params=[])
   {
       $stmt = self::$conn->prepare($sql);
       try {
           if (!$params) {
               $stmt->execute();
           } else {
               $stmt->execute($params);
           }
       } catch (\PDOException $exception) {
           // 应该是要记录到日志中，而不是直接返回
//           echo "插入数据出错:" . $exception->getMessage();
           return false;
       }
       $result = $stmt->fetch();
       return $result;
   }

    /**
     * 构造pdo对象
     */
    protected function conn()
    {
        // 初始化数据库配置
        $this->setDbConfig();
        try {
            self::$conn = new \PDO($this->dsn, $this->dbuser, $this->dbpass);
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
            self::$conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
        } catch (\PDOException $exception) {
            echo "链接数据库失败 ." . $exception->getMessage();
            exit(-1);
        }
    }

    /**
     * 设置数据库的链接配置
     */
    protected function setDbConfig()
    {
        $config = new Config();
        $dbConfig = $config['database'];
        $this->dbms = $dbConfig['type'];
        $this->dbhost = $dbConfig['hostname'];
        $this->database = $dbConfig['database'];
        $this->dbuser = $dbConfig['username'];
        $this->dbpass = $dbConfig['password'];
        $this->dbport = $dbConfig['hostport'];
        $this->dbchart = $dbConfig['charset'];
        $this->dsn = "{$this->dbms}:host={$this->dbhost};dbname={$this->database};charset={$this->dbchart}";
    }

    private function __clone()
    {

    }
}