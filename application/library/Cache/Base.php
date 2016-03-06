<?php

/**
 * 缓存管理类
 */
class Cache_Base
{

    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    protected $handler;

    /**
     * 缓存连接参数
     * @var integer
     * @access protected
     */
    protected $options = array();

    /**
     * 连接缓存
     * @access public
     * @param string $type 缓存类型
     * @param array $options 配置数组
     * @return object
     */
    public function connect($type = '', $options = array())
    {
        if (extension_loaded('memcache')) {
            $cache = new Cache_Memcachesae($options);
        } else {
            $cache = new Cache_File($options);
        }
        return $cache;
    }

    /**
     * 取得缓存类实例
     * @static
     * @access public
     * @return mixed
     */
    static function getInstance($type = '', $options = array())
    {
        static $_instance = array();
        $guid = $type . to_guid_string($options);
        if (!isset($_instance[$guid])) {
            $obj = new Cache_Base();
            $_instance[$guid] = $obj->connect($type, $options);
        }
        return $_instance[$guid];
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function __unset($name)
    {
        $this->rm($name);
    }

    public function setOptions($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function getOptions($name)
    {
        return $this->options[$name];
    }

    /**
     * 队列缓存
     * @access protected
     * @param string $key 队列名
     * @return mixed
     */
    // 
    protected function queue($key)
    {
        static $_handler = array(
            'file' => array('F', 'F'),
            'xcache' => array('xcache_get', 'xcache_set'),
            'apc' => array('apc_fetch', 'apc_store'),
        );
        $queue = isset($this->options['queue']) ? $this->options['queue'] : 'file';
        $fun = isset($_handler[$queue]) ? $_handler[$queue] : $_handler['file'];
        $queue_name = isset($this->options['queue_name']) ? $this->options['queue_name'] : 'think_queue';
        $value = $fun[0]($queue_name);
        if (!$value) {
            $value = array();
        }
        // 进列
        if (false === array_search($key, $value)) array_push($value, $key);
        if (count($value) > $this->options['length']) {
            // 出列
            $key = array_shift($value);
            // 删除缓存
            $this->rm($key);
        }
        return $fun[1]($queue_name, $value);
    }

    public function __call($method, $args)
    {
        //调用缓存类型自己的方法
        if (method_exists($this->handler, $method)) {
            return call_user_func_array(array($this->handler, $method), $args);
        } else {
            throw new Exception(__CLASS__ . ':' . $method . '_METHOD_NOT_EXIST_');
        }
    }
}