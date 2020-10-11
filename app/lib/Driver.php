<?php


namespace App\lib;

/**
 * 解析配置文件驱动类
 * Class Driver
 * @package App\lib
 */
class Driver
{
    /**
     * 创建工厂对象实例
     * @access public
     * @param  string $name         工厂类名
     * @param  string $namespace    默认命名空间
     * @return mixed
     */
    public static function factory($name, $namespace = '', ...$args)
    {
        $class = false !== strpos($name, '\\') ? $name : $namespace . ucwords($name);

        if (class_exists($class)) {
            try {
                $reflect = new \ReflectionClass($class);

                return $reflect->newInstanceArgs($args);

            } catch (\ReflectionException $e) {
                throw new \Exception('class not exists: ' . $class, $class);
            }
        } else {
            throw new \Exception('class not exists:' . $class, $class);
        }
    }
}