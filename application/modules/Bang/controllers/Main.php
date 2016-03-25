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
            //测试的时候模拟一个登陆用户
            session('userId', 6);
            if (!session('userName')) {
                cookie('nickname', 'BigBigBoy');
                cookie('headimgurl', 'http://wx.qlogo.cn/mmopen/seHTfIrWGf40t7p4lsSvY4WvMYmoZWSiaOZutbia756ORWwyuCmWMZyMYQicyxAhdRPAZWJGiciaibHr9lh5wY4mwxRax4yribGpzzQ/0');
            }
            return;
        }
        if (!session('nickname')) {
            $user = new Weixin_User_OAuthBase();
            $user->login();
        } elseif (!session('userName')) {
            $bangUserModel = new Bang_UserModel();
            $loginUserInfo = $bangUserModel
                ->findUser(array('openid' => session('openid')));
            if (!$loginUserInfo) {
                $userInfo['headimgurl'] = session('headImgUrl')?:('http://' . $_SERVER['HTTP_HOST'].'/public/bang/image/youke.jpg');
                $userInfo['nickname'] = session('nickname');
                $userInfo['join_time'] = time();
                $userInfo['openid'] = session('openid');
                $userId = $bangUserModel->addUser($userInfo);
            }
            Own_Log::getInstance()->addInfo(session('openid') . " login success");
            session('userName', $loginUserInfo['name'] ?: '');
            session('userId', $loginUserInfo['_id'] ?: $userId);
            session('nickname', $loginUserInfo['nick_name'] ?: session('nickname'));
            cookie('nickname', $loginUserInfo['nick_name'] ?: session('nickname'));
            cookie('headimgurl', $loginUserInfo['headimgurl'] ?: session('headImgUrl'));
            $this->assign('userId', session('userId'));
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
        $comments = (new Bang_CommentModel())->getComments($taskId);
        $this->assign('comments', json_encode($comments));
    }

    /**
     *处理评论提交按钮
     * 返回值对应关系：
     * errCode  errMsg
     * 0        ok
     * -1       System wrong!                系统错误，与客户端无关
     * -2       Unauthorized Access!         非法入侵访问
     * -3       Data is not legitimate!      提交的数据验证不通过
     * 2        This user is already exist!  注册的用户已经存在
     */
    public function postCommentAction()
    {
        $returnMsg['errCode'] = 0;
        $returnMsg['errMsg'] = 'ok';
        $valiRuleArr['comment'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::MIN_LEN => 1);
        $valiRuleArr['aim_comment'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR);
        $valiRuleArr['task'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR);
        $valiResult = Own_Validate::validateFuncParam(getParam('post.'), $valiRuleArr);
        if (!$valiResult) {
            $returnMsg['errCode'] = -3;
            $returnMsg['errMsg'] = 'Data is not legitimate!';
        } else if (!session('userId')) {
            $returnMsg['errCode'] = -2;
            $returnMsg['errMsg'] = 'Unauthorized Access!';
        } else {
            $commentModel = new Bang_CommentModel();
            $commentInfo['user'] = session('userId');
            $commentInfo['task'] = getParam('post.task');
            $commentInfo['aim_comment'] = getParam('post.aim_comment');
            $commentInfo['content'] = getParam('post.comment');
            $commentInfo['post_time'] = time();
            $insertId = $commentModel->insertComment($commentInfo);
            $commentInfo['_id'] = $insertId;
            $returnMsg['data'] = json_encode($commentInfo);
        }
        exit(json_encode($returnMsg));
    }
}
