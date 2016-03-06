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
        if (!function_exists('saeAutoLoader')) {// 自动识别SAE环境
            $viewPath = $this->getViewpath();
            $this->getView()->setScriptPath(($viewPath[0]) . $this->getModuleName() . '/');
        } else {
            $this->getView()->setScriptPath((APPLICATION_PATH . '/application/views/') . $this->getModuleName());
        }
    }

    function assign($name, $value)
    {
        $this->getView()->assign($name, $value);
    }


    /**
     * 统一获取参数，get、post等
     * @param $paramName string
     * @return mixed
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
