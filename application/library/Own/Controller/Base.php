<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-2-29
 * Time: 下午5:50
 */
abstract class Own_Controller_Base extends Yaf_Controller_Abstract
{
    function init()
    {
        $viewPath = $this->getViewpath();
        $this->getView()->setScriptPath(($viewPath[0]) . $this->getModuleName() . '/');
    }

    function assign($name, $value)
    {
        $this->getView()->assign($name, $value);
    }

    /**
     * session的value值只允许string，int，
     * 不允许array，本方法以此判断是否为设置值或者取值，
     * 如果为array，且为空数组，则直接返回已有的值，
     * 如果为数组且不为空，则抛出异常。
     * @param $name
     * @param array $value
     * @return bool|mixed|Yaf_Session
     * @throws \Yaf\Exception
     */
    function session($name, $value = array())
    {
        $sessionObj = Yaf_Session::getInstance();
        if (is_string($value) || is_numeric($value)) {
            return $sessionObj->set($name, $value);
        } else if (is_array($value) && !$value) {
            return $sessionObj->get($name);
        }
        throw new \Yaf\Exception('Invalid session value!');
    }

    /**
     * 统一获取参数，get、post等
     * @param $paramName string
     */
    function getParam($paramName)
    {
        $arr = explode('.', $paramName);
        if ($arr[0] = 'post') {
            return $this->getRequest()->getPost($arr[1] ?: NULL);
        } elseif ($arr[0] = 'get') {
            return $this->getRequest()->getQuery($arr[1] ?: NULL);
        } else {
            return $this->getRequest()->get($paramName);
        }
    }
}
