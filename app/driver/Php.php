<?php


namespace App\driver;

/**
 * Class Php , 修改Config类加载php配置文件的方法  Config->loadFile
 * @package App\driver
 */
class Php
{
    protected $config;

    public function __construct($config)
    {
        if (is_file($config)) {
            $config = include $config;
        }

        $this->config = $config;
    }

    public function parse()
    {
        return $this->config;
    }
}