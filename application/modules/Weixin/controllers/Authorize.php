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
                /*Own_AsyncTaskManager::addAsyncTask('WXShake\Common\ShakePlatform',
                    'syncDeviceList', array(), 20, 5, '同步摇一摇设备数据', array('authorizedAppId' => $authAppId));
                Own_AsyncTaskManager::addAsyncTask('WXShake\Common\ShakePlatform',
                    'syncPageList', array(), 20, 5, '同步摇一摇页面数据', array('authorizedAppId' => $authAppId));
                Own_AsyncTaskManager::addAsyncTask('Platform\Common\WXStoreManage\StoreManage',
                    'syncPoiList', array(), 20, 5, '同步门店数据', array('authorizedAppId' => $authAppId));*/
            }
            header("Content-Type: text/html; charset=utf-8");
            echo '您的摇一摇授权已成功！谢谢您！';
        } else {
            $this->redirect('/Weixin/Authorize/authorize');
        }
    }


    public function testAction()
    {
        $authAppId = 'wxc0dfd0ee0eb3a26b';
        $createTableState = $this->createDataBaseTable($authAppId);
        if ($createTableState) {
            //数据表创建成功以后，同步数据（当前同步摇一摇数据）
            /*Own_AsyncTaskManager::addAsyncTask('WXShake\Common\ShakePlatform',
                'syncDeviceList', array(), 20, 5, '同步摇一摇设备数据', array('authorizedAppId' => $authAppId));
            Own_AsyncTaskManager::addAsyncTask('WXShake\Common\ShakePlatform',
                'syncPageList', array(), 20, 5, '同步摇一摇设备数据', array('authorizedAppId' => $authAppId));
            Own_AsyncTaskManager::addAsyncTask('Platform\Common\WXStoreManage\StoreManage',
                'syncPoiList', array(), 20, 5, '同步门店数据', array('authorizedAppId' => $authAppId));*/
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
        ///创建数据库表的sql语句
        /*        $tableSqlArr['shake_device_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_shake_device`(
          `device_id` int(11) NOT NULL,
          `major` int(11) NOT NULL,
          `minor` int(11) NOT NULL,
          `status` char(1) NOT NULL,
          `poi_id` int(11) NOT NULL,
          `uuid` varchar(36) NOT NULL,
          `comment` varchar(30) DEFAULT NULL,
          `page_ids` varchar(10000) DEFAULT NULL ,
          `group_id` int(11) DEFAULT '0',
          `last_active_time` bigint(20) NOT NULL,
          PRIMARY KEY (`device_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['shake_device_extra_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_shake_device_extra` (
          `device_id` int(11) NOT NULL,
          `major` int(11) NOT NULL,
          `minor` int(11) NOT NULL,
          `device_sn` char(30) DEFAULT NULL,
          `visit_num` int(11) DEFAULT '0',
          `live_num` int(11) DEFAULT '0',
          PRIMARY KEY (`device_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['shake_device_group_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_shake_device_group`  (
          `group_id` int(10) unsigned NOT NULL,
          `group_name` char(100) NOT NULL,
          `device_ids` varchar(10000) DEFAULT NULL ,
          PRIMARY KEY (`group_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['shake_device_apply_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_shake_device_apply` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `handle_state` int(11) NOT NULL DEFAULT '0',
          `device_num` int(11) NOT NULL,
          `poi_id` int(11) NOT NULL DEFAULT '0',
          `comment` char(30) DEFAULT NULL,
          `apply_time` bigint(20) NOT NULL,
          `apply_id` int(11) NOT NULL,
          `audit_status` int(11) NOT NULL,
          `audit_comment` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['shake_page_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_shake_page` (
          `page_id` int(11) NOT NULL,
          `page_url` text NOT NULL,
          `icon_url` text NOT NULL,
          `title` varchar(12) NOT NULL,
          `description` varchar(14) NOT NULL,
          `comment` varchar(30) NOT NULL,
          `device_ids` varchar(10000) DEFAULT NULL ,
          `create_time` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`page_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['shake_page_extra_table'] = <<<DB
        CREATE TABLE  IF NOT EXISTS `think_{$serverName}_shake_page_extra` (
          `page_id` int(11) NOT NULL,
          `display_num` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`page_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;
                $tableSqlArr['poi_table'] = <<<DB
        CREATE TABLE IF NOT EXISTS `think_{$serverName}_poi` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `sid` int(11) NOT NULL,
          `business_name` char(50) NOT NULL,
          `branch_name` char(50) NOT NULL,
          `address` text NOT NULL,
          `telephone` char(20) NOT NULL,
          `categories` text NOT NULL,
          `city` char(30) NOT NULL,
          `province` varchar(30) NOT NULL,
          `offset_type` int(11) NOT NULL,
          `longitude` char(20) NOT NULL,
          `latitude` char(20) NOT NULL,
          `photo_list` text NOT NULL,
          `introduction` text NOT NULL,
          `recommend` text NOT NULL,
          `special` text NOT NULL,
          `open_time` char(20) NOT NULL,
          `avg_price` int(11) NOT NULL,
          `poi_id` int(11) NOT NULL,
          `available_state` int(11) NOT NULL,
          `district` char(20) NOT NULL,
          `update_status` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
        DB;*/
        ///
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
