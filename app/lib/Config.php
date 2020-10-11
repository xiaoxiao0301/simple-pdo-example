<?php


namespace App\lib;

/**
 * 获取配置文件类
 *
 * Class Config
 * @package lib
 */
class Config implements \ArrayAccess
{
    /**
     * 配置前缀
     * @var string
     */
    protected $prefix = 'app';
    /**
     * @var string 配置文件后缀
     */
    protected $ext = ".php";
    /**
     * @var string 配置文件目录
     */
    protected $path;
    /**
     * @var array 配置项
     */
    protected $config = [];

    /**
     * 初始化配置文件
     * Config constructor.
     * @param string $path
     */
    public function __construct($path = '')
    {
       $this->initConfigDir($path);
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     * @access public
     * @param  string|array  $name 配置参数名（支持三级配置 .号分割）
     * @param  mixed         $value 配置值
     * @return mixed
     */
    public function set($name, $value = null)
    {
        if (is_string($name)) {
            if (false === strpos($name, '.')) {
                $name = $this->prefix . '.' . $name;
            }

            $name = explode('.', $name, 3);

            if (count($name) == 2) {
                $this->config[strtolower($name[0])][$name[1]] = $value;
            } else {
                $this->config[strtolower($name[0])][$name[1]][$name[2]] = $value;
            }

            return $value;
        } elseif (is_array($name)) {
            // 批量设置
            if (!empty($value)) {
                if (isset($this->config[$value])) {
                    $result = array_merge($this->config[$value], $name);
                } else {
                    $result = $name;
                }

                $this->config[$value] = $result;
            } else {
                $result = $this->config = array_merge($this->config, $name);
            }
        } else {
            // 为空直接返回 已有配置
            $result = $this->config;
        }

        return $result;
    }

    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string    $name      配置参数名
     * @param  mixed     $default   默认值
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        // 无参数时获取所有
        if (empty($name)) {
            return $this->config;
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);
        $config  = $this->config;

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }


    /**
     * 移除配置
     * @access public
     * @param  string  $name 配置参数名（支持三级配置 .号分割）
     * @return void
     */
    public function remove($name)
    {
        if (false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name;
        }

        $name = explode('.', $name, 3);

        if (count($name) == 2) {
            unset($this->config[strtolower($name[0])][$name[1]]);
        } else {
            unset($this->config[strtolower($name[0])][$name[1]][$name[2]]);
        }
    }


    /**
     * 检测配置是否存在
     * @access public
     * @param  string    $name 配置参数名（支持多级配置 .号分割）
     * @return bool
     */
    public function has($name)
    {
        if (false === strpos($name, '.')) {
            $name = $this->prefix . '.' . $name;
        }

        return !is_null($this->get($name));
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetGet($key)
    {
        return $this->get($key);
    }

    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    private function initConfigDir(string $path)
    {
        if (!$path) {
            $path = __DIR__ . "/../config/";
        }
        $this->path = $path;
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
               $this->load($path . $file, pathinfo($file, PATHINFO_FILENAME));
            }
        }
    }

    /**
     * 加载配置文件
     * @access public
     * @param  string    $file 配置文件名
     * @param  string    $name 一级配置名
     * @return mixed
     */
    public function load($file, $name = '')
    {
        if (is_file($file)) {
            $filename = $file;
        } elseif (is_file($this->path . $file . $this->ext)) {
            $filename = $this->path . $file . $this->ext;
        }

        return $this->loadFile($filename, $name);
    }

    /**
     * 解析配置文件中的内容
     * @param $file
     * @param $name
     * @return mixed
     */
    protected function loadFile($file, $name)
    {
        $name = strtolower($name);
        $type = pathinfo($file, PATHINFO_EXTENSION);
        return $this->parse($file, $type, $name);
    }

    /**
     * 解析配置文件或内容
     * @access public
     * @param  string    $config 配置文件路径或内容
     * @param  string    $type 配置解析类型
     * @param  string    $name 配置名（如设置即表示二级配置）
     * @return mixed
     */
    public function parse($config, $type = '', $name = '')
    {
        if (empty($type)) {
            $type = pathinfo($config, PATHINFO_EXTENSION);
        }

        $object = Driver::factory($type, '\\App\\driver\\', $config);

        return $this->set($object->parse(), $name);
    }
}