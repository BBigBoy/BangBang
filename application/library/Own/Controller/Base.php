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
        /**
         * 如果是Ajax请求, 则关闭HTML输出
         */
        if ($this->getRequest()->isXmlHttpRequest()) {
            Yaf_Dispatcher::getInstance()->disableView();
            return;
        }
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

}
