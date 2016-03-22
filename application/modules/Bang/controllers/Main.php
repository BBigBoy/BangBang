<?php

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
                Own_Log::getInstance()->addInfo(session('openid') . " login success");
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

    public function taskDetailAction()
    {
        $taskId = getParam('get.taskId');
        $taskInfo = (new Bang_TaskModel())->findTask($taskId);
        $this->assign("task", $taskInfo);
        $taskStatus = 0;
        $this->assign('task_status_text', '领取任务');
        if ($taskInfo['status'] == 1) {
            $taskStatus = 1;//已被领取
            $this->assign('task_status_text', '任务已被领取');
        } else if ($taskInfo['status'] == 2) {
            $taskStatus = 2;//已被取消
            $this->assign('task_status_text', '任务已取消');
        } else if ($taskInfo['time_end'] < time() && $taskInfo['status'] == 0) {
            $taskStatus = 3;//已过期
            $this->assign('task_status_text', '任务已过期');
        }
        $this->assign('task_status', $taskStatus);
    }

}
