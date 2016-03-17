<?php

class AuthorizeController extends Own_Controller_Base
{
    public function indexAction()
    {
    }

    /**
     *公众号授权时访问此链接，公众号管理员输入账号密码后会跳转到下面的redirect里面，表示授权成功！
     */
    public function authorizeAction()
    {
        $preAuthcode = getPreAuthCode();
        $redirect_uri = U('/Weixin/Authorize/authorizeSuccess/');
        $getParamArr['component_appid'] = C('APP_ID');
        $getParamArr['pre_auth_code'] = $preAuthcode;
        $getParamArr['redirect_uri'] = $redirect_uri;
        header('Location:https://mp.weixin.qq.com/cgi-bin/componentloginpage'
            . arrToGetParamStr($getParamArr));
    }

    /**
     *授权成功时会回调到此页面
     */
    public function authorizeSuccessAction()
    {
        $authInfo = getAuthInfoByAuthCode($_GET['auth_code']);
        if ($authInfo) {
            $authAppId = $authInfo['authorizerappid'];
            $createTableState = $this->createDataBaseTable($authAppId);
            if ($createTableState) {
                //数据表创建成功以后，同步数据（当前同步摇一摇数据）
                $taskInfo[] = array(
                    'url' =>
                        U("/Shake/Init/syncDeviceList"
                            , array('appid' => $authAppId),false));
                $taskInfo[] = array(
                    'url' =>
                        U("/Shake/Init/syncPageList"
                            , array('appid' => $authAppId),false));
                $taskInfo[] = array(
                    'url' =>
                        U("/Shake/Init/syncPoiList"
                            , array('appid' => $authAppId),false));
                addTaskQueue($taskInfo);
            }
            header("Content-Type: text/html; charset=utf-8");
            echo '您的微信授权已成功！谢谢您！';
            return false;
        } else {
            $this->redirect('/Weixin/Authorize/authorize');
        }
    }


    public function testAction()
    {
        $authAppId = 'wx477688baf3a4a9f6';
        $createTableState = $this->createDataBaseTable($authAppId);
        if ($createTableState) {
            //数据表创建成功以后，同步数据（当前同步摇一摇数据）
            //数据表创建成功以后，同步数据（当前同步摇一摇数据）
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncDeviceList"
                        , array('appid' => $authAppId),false));
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncPageList"
                        , array('appid' => $authAppId),false));
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncPoiList"
                        , array('appid' => $authAppId),false));
            addTaskQueue($taskInfo);
        }
        header("Content-Type: text/html; charset=utf-8");
        echo '您的摇一摇授权已成功！谢谢您！';
        return false;
    }

    /**
     * 创建属于该公众账号的各种数据表
     * @param  string $serverName [数据表名称的后缀]
     * @return bool [返回是否创建成功的信息]
     * @throws Exception
     * @throws Throwable
     */
    private function createDataBaseTable($serverName)
    {
        $pdo = \Illuminate\Database\Capsule\Manager::schema()->getConnection()->getPdo();
        $pdo->beginTransaction();
        try {
            (new Weixin_Shake_DeviceModel($serverName))->createTable();
            (new Weixin_Shake_DeviceExtraModel($serverName))->createTable();
            (new Weixin_Shake_DeviceGroupModel($serverName))->createTable();
            (new Weixin_Shake_DeviceApplyModel($serverName))->createTable();
            (new Weixin_Shake_PageModel($serverName))->createTable();
            (new Weixin_Shake_PageExtraModel($serverName))->createTable();
            (new Weixin_PoiModel($serverName))->createTable();
            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

}
