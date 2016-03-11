<?php

/**
 * @name MainController
 * @author root
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class MainController extends Own_Controller_Base
{
    private static $TASK_CATEGORY = array('线上完成', '物流托运', '帮取火车票');

    /**
     *用户登录初始化。对于游客，生成session('openid')；
     * 会员用户，则将会员信息记录到session
     */
    public function init()
    {
        parent::init();
        if (getParam('get.ii')) {
            return;
        }
        if (!session('nickname')) {
            $user = new Weixin_User_OAuthBase();
            $user->login();
        } elseif (!session('userName')) {
            $bangUserModel = new Bang_UserModel();
            $loginUserInfo = $bangUserModel
                ->findUser(array('openid' => session('openid')));
            if ($loginUserInfo) {
                session('userName', $loginUserInfo['name']);
                session('userId', $loginUserInfo['_id']);
                session('nickname', $loginUserInfo['nick_name']);
                $this->assign('userId', $loginUserInfo['_id']);
            }
        }
        $jssdk = new Weixin_JS_SDK(C('AUTH_APP_ID'));
        $signPackage = $jssdk->getSignPackage();
        $this->assign('signPackage', $signPackage);
    }

    public function indexAction()
    {
        $taskAtt = (new Bang_TaskModel())->getTasks();
        $this->assign("task_categorys", self::$TASK_CATEGORY);
        $this->assign("tasks", $taskAtt);
    }

}
