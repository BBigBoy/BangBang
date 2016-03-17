<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Own_Controller_Base
{


    /**
     * 默认动作
     * Yaf支持直接把Yaf_Request_Abstract::getParam()得到的同名参数作为Action的形参
     * 对于如下的例子, 当访问http://yourhost/bigbigboy/Index/Index/Index/name/root 的时候, 你就会发现不同
     */
    public function indexAction()
    {
        sendMail("测试");
    }

    function infoAction()
    {
        phpinfo();
        return false;
    }

    function logAction()
    {
        errorLog('get--->' . getParam("get.") . "\npost--->" . getParam("post."));
        return false;
    }
}
