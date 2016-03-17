<?php

/**
 * @name IndexController
 * @author root
 * @desc 默认控制器
 * @see http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */
class InitController extends Own_Controller_Base
{
    public function syncDeviceListRelationPageAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncDeviceList_RelationPage();
        return false;
    }

    public function syncDeviceExtraTableAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncDeviceExtraTable();
        return false;
    }

    public function syncDeviceListGroupListAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncDeviceList_GroupList();
        return false;
    }

    public function syncDeviceListGroupListDetailAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncDeviceList_GroupList_Detail();
        return false;
    }

    public function syncPageListRelationDeviceAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncPageList_RelationDevice();
        return false;
    }

    public function syncPageExtraTableAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncPageExtraTable();
        return false;
    }

    public function syncDeviceListAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncDeviceList();
        return false;
    }

    public function syncPageListAction()
    {
        $authAppId = getParam('get.appid');
        $shakePlatform = new Weixin_ShakeAround_ShakePlatform($authAppId);
        $shakePlatform->syncPageList();
        return false;
    }

    public function syncPoiListAction()
    {
        $authAppId = getParam('get.appid');
        $storeManage = new Weixin_StoreManage_StoreManage($authAppId);
        $storeManage->syncPoiList();
        return false;
    }


}
