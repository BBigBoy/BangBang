<?php

/**
 * @name IndexController
 * @author root
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class IndexController extends Own_Controller_Base
{

    //TODO:对于火车票、注册等，统一使用一个接口，不要每项业务都去操作数据库。否则不好控制
    private static $TASK_CATEGORY = array('线上完成', '物流托运', '帮取火车票');

    public function indexAction()
    {
    }

    /**
     *发布任务
     */
    public function publishAction()
    {
        $this->assign("task_categorys", self::$TASK_CATEGORY);
    }

    /**
     *注册
     */
    public function registerAction()
    {
    }

    /**
     *领取任务
     */
    public function getAction()
    {
        $taskAtt = (new Bang_TaskModel())->getTasks();
        $this->assign("task_categorys", self::$TASK_CATEGORY);
        $this->assign("tasks", $taskAtt);
    }

    /**
     *帮取火车票业务
     */
    public function huochepiaoAction()
    {

    }

    /**
     *叫车业务
     */
    public function jiaocheAction()
    {

    }

    /**
     *帮取火车票业务--团委页面
     */
    public function huochepiaotuanweiAction()
    {
        //检测免费领取火车票业务是否使用
        $taskInfo['publish_user'] = 1;//session('userId')
        $taskInfo['publish_user_openid'] = 'osKdtuHwxjhA9uAJsGS7nbu27XQg';//session('openid')
        $taskInfo['activity_id'] = 1;
        $task = (new Bang_TaskModel())->findTask($taskInfo);
        if ($task) {
            $this->assign('huochepiao_had_use', 'yes');
        }
    }

    /**
     *免费帮取火车票业务
     * 返回值对应关系：
     * errCode  errMsg
     * 0        ok
     * -1       System wrong!                系统错误，与客户端无关
     * -2       Unauthorized Access!         非法入侵访问
     * -3       Data is not legitimate!      提交的数据验证不通过
     * 2        This user is already exist!  注册的用户已经存在
     */
    public function huochepiaogetAction()
    {
        $returnMsg['errCode'] = 0;
        $returnMsg['errMsg'] = 'ok';
        $valiRuleArr['tel'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::FORMAT => Own_Validate::MOBILE_FORMAT);
        $valiRuleArr['name'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::MIN_LEN => 1);
        $valiResult = Own_Validate::validateFuncParam(getParam('post.'), $valiRuleArr);
        if (!$valiResult) {
            $returnMsg['errCode'] = -3;
            $returnMsg['errMsg'] = 'Data is not legitimate!';
        } else if (!session('openid')) {
            $returnMsg['errCode'] = -2;
            $returnMsg['errMsg'] = 'Unauthorized Access!';
        } else {
            $userModle = new Bang_UserModel();
            $userInfo['openid'] = 'osKdtuHwxjhA9uAJsGS7nbu27XQg';// session('openid');
            $findUserInfo = $userModle->findUser($userInfo);
            if (!$findUserInfo) {
                $userInfo['name'] = getParam('post.name');
                $userInfo['nick_name'] = session('nickname');
                $userInfo['tel'] = getParam('post.tel');
                $userInfo['level'] = 0;
                $userInfo['last_login_time'] = time();
                $userInfo['join_time'] = time();
                $userId = $userModle->addUser($userInfo);
                if (!$userId) {
                    $returnMsg['errCode'] = -1;
                    $returnMsg['errMsg'] = 'System wrong1!';
                } else {
                    session('userId', $userId);
                }
            } else {
                session('userId', $findUserInfo['_id']);
            }
            if (session('userId')) {
                $taskInfo['publish_user'] = session('userId');
                $taskInfo['publish_user_openid'] = session('openid');
                $taskInfo['activity_id'] = 1;
                $taskModel = new Bang_TaskModel();
                $hadUse = $taskModel->findTask($taskInfo);
                if ($hadUse) {
                    $returnMsg['errCode'] = 2;
                    $returnMsg['errMsg'] = 'had used!';
                } else {
                    $taskInfo['category'] = 2;
                    $taskInfo['instruction'] = "取火车票";
                    $taskInfo['reward'] = 0;
                    $taskInfo['comment'] = '统一免费';
                    $taskInfo['status'] = 0;
                    $taskInfo['time_start'] = time();
                    $taskInfo['time_end'] = 1453651200;
                    $taskInfo['loc_goal_province'] = '陕西';
                    $taskInfo['loc_goal_city'] = '咸阳';
                    $taskInfo['loc_goal_district'] = '杨陵区';
                    $taskInfo['loc_goal_description'] = '火车站';
                    $addState = $taskModel->addTask($taskInfo);
                    if (!$addState) {
                        $returnMsg['errCode'] = -1;
                        $returnMsg['errMsg'] = 'System wrong2!';
                    }
                }
            }
        }
        exit(json_encode($returnMsg));
    }

    /**
     *免费帮取火车票业务
     * 返回值对应关系：
     * errCode  errMsg
     * 0        ok
     * -1       System wrong!                系统错误，与客户端无关
     * -2       Unauthorized Access!         非法入侵访问
     * -3       Data is not legitimate!      提交的数据验证不通过
     * 2        This task is already exist!  已经注册过这个业务
     */
    public function huochepiaogetstraightAction()
    {
        $modle = new Bang_TaskModel();
        $returnMsg['errCode'] = 0;
        $returnMsg['errMsg'] = 'ok';
        if (!session('userId')) {
            $returnMsg['errCode'] = -2;
            $returnMsg['errMsg'] = 'Unauthorized Access!';
        } else {
            $taskInfo['publish_user'] = session('userId');
            $taskInfo['publish_user_openid'] = session('openid');
            $taskInfo['activity_id'] = 1;
            $hadUse = $modle->findTask($taskInfo);
            if ($hadUse) {
                $returnMsg['errCode'] = 2;
                $returnMsg['errMsg'] = 'had used!';
            } else {
                $taskInfo['category'] = 2;
                $taskInfo['instruction'] = "取火车票";
                $taskInfo['reward'] = 0;
                $taskInfo['comment'] = '统一免费';
                $taskInfo['status'] = 0;
                $taskInfo['time_start'] = time();
                $taskInfo['time_end'] = 1453651200;
                $taskInfo['loc_goal_province'] = '陕西';
                $taskInfo['loc_goal_city'] = '咸阳';
                $taskInfo['loc_goal_district'] = '杨陵区';
                $taskInfo['loc_goal_description'] = '火车站';
                $addState = $modle->addTask($taskInfo);
                if (!$addState) {
                    $returnMsg['errCode'] = -1;
                    $returnMsg['errMsg'] = 'System wrong!';
                }
            }
        }
        exit(json_encode($returnMsg));
    }

    /**
     *处理任务发布页面提交按钮事件
     * 返回值对应关系：
     * errCode  errMsg
     * 0        ok
     * -1       System wrong!                系统错误，与客户端无关
     * -2       Unauthorized Access!         非法入侵访问
     * -3       Data is not legitimate!      提交的数据验证不通过
     * 2        This user is already exist!  注册的用户已经存在
     */
    public function publishsubmitAction()
    {
        $returnMsg['errCode'] = 0;
        $returnMsg['errMsg'] = 'ok';
        $commonValiVars = array('category', 'category_index', 'instruction',
            'reward', 'year_end', 'month_end', 'day_end');
        $valiRuleArr['comment'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::IS_NULL => true);
        if (getParam('post.category') == '物流托运' || getParam('post.category') == '帮取火车票') {
            $commonValiVars[] = 'prov_goal';
            $commonValiVars[] = 'city_goal';
            $valiRuleArr['dist_goal'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::IS_NULL => true);
            $commonValiVars[] = 'area_detail_goal';
            if (getParam('post.category') == '物流托运') {
                $commonValiVars[] = 'prov_start';
                $commonValiVars[] = 'city_start';
                $valiRuleArr['dist_start'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::IS_NULL => true);
                $commonValiVars[] = 'area_detail_start';
            }
        }
        $valiRuleArr[Own_Validate::COMMON_VALI] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR,
            Own_Validate::MIN_LEN => 1,
            Own_Validate::COMMON_VALI_VARS => $commonValiVars);
        $valiResult = (self::$TASK_CATEGORY[getParam('post.category_index')] == getParam('post.category')) && Own_Validate::validateFuncParamInner(getParam('post.'), $valiRuleArr);
        if (!$valiResult) {
            $returnMsg['errCode'] = -3;
            $returnMsg['errMsg'] = 'Data is not legitimate!';
        } else if (!session('openid')) {
            $returnMsg['errCode'] = -2;
            $returnMsg['errMsg'] = 'Unauthorized Access!';
        } else {
            $taskInfo['publish_user'] = session('userId') ?: 0;
            $taskInfo['publish_user_openid'] = session('openid');
            $taskInfo['category'] = getParam('post.category_index');
            $taskInfo['instruction'] = getParam('post.instruction');
            $taskInfo['reward'] = ((float)getParam('post.reward')) * 100;
            if (getParam('post.comment')) {
                $taskInfo['comment'] = getParam('post.comment');
            }
            $taskInfo['status'] = 0;
            $taskInfo['time_start'] = time();
            $taskInfo['time_end'] = mktime(23, 59, 59, getParam('post.month_end'), getParam('post.day_end'), getParam('post.year_end'));
            if (getParam('post.category') == '物流托运' || getParam('post.category') == '帮取火车票') {
                $taskInfo['loc_goal_province'] = getParam('post.prov_goal');
                $taskInfo['loc_goal_city'] = getParam('post.city_goal');
                $taskInfo['loc_goal_district'] = getParam('post.dist_goal');
                $taskInfo['loc_goal_description'] = getParam('post.area_detail_goal');
                if (getParam('post.category') == '物流托运') {
                    $taskInfo['loc_start_province'] = getParam('post.prov_start');
                    $taskInfo['loc_start_city'] = getParam('post.city_start');
                    $taskInfo['loc_start_district'] = getParam('post.dist_start');
                    $taskInfo['loc_start_description'] = getParam('post.area_detail_start');
                }
            }
            $addState = (new Bang_TaskModel())->addTask($taskInfo);
            if (!$addState) {
                $returnMsg['errCode'] = -1;
                $returnMsg['errMsg'] = 'System wrong!';
            }
        }
        exit(json_encode($returnMsg));
    }

    /**
     *处理注册页面提交按钮
     * 返回值对应关系：
     * errCode  errMsg
     * 0        ok
     * -1       System wrong!                系统错误，与客户端无关
     * -2       Unauthorized Access!         非法入侵访问
     * -3       Data is not legitimate!      提交的数据验证不通过
     * 2        This user is already exist!  注册的用户已经存在
     */
    public function registersubmitAction()
    {
        $returnMsg['errCode'] = 0;
        $returnMsg['errMsg'] = 'ok';
        $valiRuleArr[Own_Validate::COMMON_VALI] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::MIN_LEN => 1,
            Own_Validate::COMMON_VALI_VARS => array('name', 'nickname'));
        $valiRuleArr['personalized_signature'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR);
        $valiRuleArr['email'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::FORMAT => Own_Validate::EMAIL_FORMAT);
        $valiRuleArr['tel'] = array(Own_Validate::TYPE => Own_Validate::STRING_VAR, Own_Validate::FORMAT => Own_Validate::MOBILE_FORMAT);
        $valiResult = Own_Validate::validateFuncParam(getParam('post.'), $valiRuleArr);
        if (!$valiResult) {
            $returnMsg['errCode'] = -3;
            $returnMsg['errMsg'] = 'Data is not legitimate!';
        } else if (!session('openid')) {
            $returnMsg['errCode'] = -2;
            $returnMsg['errMsg'] = 'Unauthorized Access!';
        } else {
            $UserModel = new Bang_UserModel();
            $userInfo['openid'] = session('openid');
            $findUserInfo = $UserModel->findUser($userInfo);
            if ($findUserInfo) {
                $returnMsg['errCode'] = 2;
                $returnMsg['errMsg'] = 'This user is already exist!';
            } else {
                $userInfo['name'] = getParam('post.name');
                $userInfo['nick_name'] = getParam('post.nickname');
                $userInfo['email'] = getParam('post.email');
                $userInfo['tel'] = getParam('post.tel');
                $userInfo['personalized_signature'] = getParam('post.personalized_signature');
                $userInfo['level'] = 0;
                $userInfo['last_login_time'] = time();
                $userInfo['join_time'] = time();
                $addState = $UserModel->addUser($userInfo);
                if (!$addState) {
                    $returnMsg['errCode'] = -1;
                    $returnMsg['errMsg'] = 'System wrong!';
                }
            }
        }
        exit(json_encode($returnMsg));
    }


    /**
     *信息提示
     */
    public function msgAction()
    {

    }
}
