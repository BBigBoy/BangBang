<?php

/**
 * Memcache缓存驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Cache
 * @author    liu21st <liu21st@gmail.com>
 */
class Cache_Memcachesae extends Cache_Base
{

    /**
     * 架构函数
     * @param array $options 缓存参数
     * @access public
     */
    function __construct($options = array())
    {
        $cacheConfig = new Yaf_Config_Ini(APPLICATION_PATH . '/conf/cache.ini', 'cache');
        $options = array_merge(array(
            'host' => $cacheConfig['memcache']['host'] ?: '127.0.0.1',
            'port' => $cacheConfig['memcache']['port'] ?: 11211,
            'timeout' => $cacheConfig['cache']['timeout'] ?: false,
            'persistent' => false,
        ), $options);

        $this->options = $options;
        $this->options['expire'] = isset($options['expire']) ? $options['expire'] : $cacheConfig['cache']['expire'];
        $this->options['prefix'] = isset($options['prefix']) ? $options['prefix'] : $cacheConfig['cache']['prefix'];
        $this->options['length'] = isset($options['length']) ? $options['length'] : 0;
        $this->handler = memcache_init();//[sae] 下实例化
        //[sae] 下不用链接
        $this->connected = true;
    }

    /**
     * 是否连接
     * @access private
     * @return boolean
     */
    private function isConnected()
    {
        return $this->connected;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function get($name)
    {
        return $this->handler->get($_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'] . $name);
    }

    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param integer $expire 有效时间（秒）
     * @return boolean
     */
    public function set($name, $value, $expire = null)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        $name = $this->options['prefix'] . $name;
        if ($this->handler->set($_SERVER['HTTP_APPVERSION'] . '/' . $name, $value, 0, $expire)) {
            if ($this->options['length'] > 0) {
                // 记录缓存队列
                $this->queue($name);
            }
            return true;
        }
        return false;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm($name, $ttl = false)
    {
        $name = $_SERVER['HTTP_APPVERSION'] . '/' . $this->options['prefix'] . $name;
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    public function clear()
    {
        return $this->handler->flush();
    }

    /**
     * 队列缓存
     * @access protected
     * @param string $key 队列名
     * @return mixed
     */
    //[sae] 下重写queque队列缓存方法
    protected function queue($key)
    {
        $queue_name = isset($this->options['queue_name']) ? $this->options['queue_name'] : 'think_queue';
        $value = F($queue_name);
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
        return F($queue_name, $value);
    }

}
