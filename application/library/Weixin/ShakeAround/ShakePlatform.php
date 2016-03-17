<?php

class Weixin_ShakeAround_ShakePlatform
{

    /**
     * @var string 授权的第三方公众号APPID
     */
    private $authorizedAppId;

    /**
     * @param $authorizedAppId string 授权公众账号的appId
     */
    function __construct($authorizedAppId)
    {
        $this->authorizedAppId = $authorizedAppId;
    }

    /**
     * 同步数据库中设备与页面的关联关系
     * @param $pageIdAtt array 需要同步的页面ID数组
     * @param $deviceIdAtt array 需要同步的设备ID数组(允许只同步页面的绑定信息)
     * @param string $authAppId
     * @return bool 成功返回true，失败返回false
     */
    public static function syncBindRelationDeviceAndPage($pageIdAtt, $deviceIdAtt = array(), $authAppId = '')
    {
        $authAppId = $authAppId ?: session('user.app_id');
        $returnResult = true;
        if ($pageIdAtt && is_array($pageIdAtt)) {
            $returnResult = ($returnResult && self::syncPageBindRelation($pageIdAtt, $authAppId));
        }
        if ($deviceIdAtt && is_array($deviceIdAtt)) {
            $returnResult = ($returnResult && self::syncDeviceBindRelation($deviceIdAtt, $authAppId));
        }
        return $returnResult;
    }

    /**
     * 同步数据库中页面的关联关系
     * @param $pageIdAtt array 需要同步的页面ID数组
     * @param $authAppId string 授权的公众账号AppId
     * @return bool 成功返回true，失败返回false
     */
    public static function syncPageBindRelation($pageIdAtt, $authAppId = '')
    {
        $returnResult = true;
        $authAppId = $authAppId ?: session('user.app_id');
        if ($authAppId && $pageIdAtt && is_array($pageIdAtt)) {
            $shakeAround = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
            ///同步页面信息
            $searchPageBindDevicesParam['type'] = 2;
            $shakePageModel = new Weixin_Shake_PageModel($authAppId);
            foreach ($pageIdAtt as $pageId) {
                $searchPageBindDevicesParam['page_id'] = (int)$pageId;
                $searchPageBindDevicesParam['begin'] = 0;
                $searchPageBindDevicesParam['count'] = 50;
                $searchResult = $shakeAround->searchRelation($searchPageBindDevicesParam);
                if ($searchResult) {
                    $relationAtt = $searchResult['data']['relations'];
                    $deviceIdStr = '';
                    foreach ($relationAtt as $relation) {
                        $deviceIdStr .= ($relation['device_id'] . ',');
                    }
                    $totalCount = $searchResult['data']['total_count'];
                    $getCount = 50;
                    while ($totalCount > $getCount) {
                        $searchPageBindDevicesParam['begin'] = $getCount;
                        $getCount += 50;
                        $searchResult = $shakeAround->searchRelation($searchPageBindDevicesParam);
                        if ($searchResult) {
                            $relationAtt = $searchResult['data']['relations'];
                            foreach ($relationAtt as $relation) {
                                $deviceIdStr .= ($relation['device_id'] . ',');
                            }
                        } else {
                            errorLog('pageId:' . $pageId . '<--->$getCount:' . $getCount . '<--->$totalCount:' . $totalCount);
                            $returnResult = false;
                            continue;
                        }
                    }
                    $deviceIdStr = trim($deviceIdStr, ',');
                    $wherePage['page_id'] = $pageId;
                    $pageInfo['device_ids'] = $deviceIdStr;
                    //存在则更新，不存在则插入
                    $updateState = $shakePageModel->updatePage($wherePage, $pageInfo);
                    if ($updateState === false) {
                        errorLog('pageId:' . $pageId . '<-->updatePageInfo Error1!');
                        $returnResult = false;
                        continue;
                    }
                } else {
                    errorLog('pageId:' . $pageId . '<-->search Page Relation Error!');
                    $returnResult = false;
                    continue;
                }
            }
        } else {
            if (!is_array($pageIdAtt)) {
                errorLog('pageID列表传输错误！ pageIdAtt：' . json_encode($pageIdAtt));
                $returnResult = false;
            }
        }
        return $returnResult;
    }

    /**
     * 同步数据库中设备的关联关系
     * @param $deviceIdAtt array 需要同步的设备ID数组(允许只同步页面的绑定信息)
     * @param $authAppId string 授权的公众账号AppId
     * @return bool 成功返回true，失败返回false
     */
    public
    static function syncDeviceBindRelation($deviceIdAtt, $authAppId = '')
    {
        $returnResult = true;
        $authAppId = $authAppId ?: session('user.app_id');
        if ($authAppId && $deviceIdAtt && is_array($deviceIdAtt)) {
            $shakeAround = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
            $searchDeviceBindPagesParam['type'] = 1;
            $shakeDeviceModel = new Weixin_Shake_DeviceModel($authAppId . 'ShakeDevice');
            foreach ($deviceIdAtt as $deviceId) {
                $searchDeviceBindPagesParam['device_identifier']['device_id'] = (int)$deviceId;
                $searchResult = $shakeAround->searchRelation($searchDeviceBindPagesParam);
                if ($searchResult) {
                    $relationAtt = $searchResult['data']['relations'];
                    $pageIdStr = '';
                    foreach ($relationAtt as $relation) {
                        $pageIdStr .= ($relation['page_id'] . ',');
                    }
                    $pageIdStr = trim($pageIdStr, ',');
                    $whereDevice['device_id'] = $deviceId;
                    $saveDeviceInfo['page_ids'] = $pageIdStr;
                    $updateState = $shakeDeviceModel->updateDevice($whereDevice, $saveDeviceInfo);
                    if ($updateState === false) {
                        errorLog('$deviceId:' . $deviceId . '<-->update deviceId Error!');
                        $returnResult = false;
                        continue;
                    }
                } else {
                    errorLog('deviceId:' . $deviceId . '<-->search Device Relation Error!');
                    $returnResult = false;
                    continue;
                }
            }
        } else {
            if (!is_array($deviceIdAtt)) {
                errorLog('deviceId列表传输错误！ deviceIdAtt：' . json_encode($deviceIdAtt));
                $returnResult = false;
            }
        }
        return $returnResult;
    }

    /**
     * 更新设备绑定信息。
     * 传入设备应该绑定的页面数组以及制定设备即可自动更新
     * @param $pageIdAtt
     * @param $deviceId
     * @param string $authAppId
     * @return bool
     */
    public static function updateDeviceBindInfo($pageIdAtt, $deviceId, $authAppId = '')
    {
        $authAppId = $authAppId ?: session('user.app_id');
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
        $unbindReturnContentAtt = $shake->bindPageDevice($pageIdAtt, (int)$deviceId);
        if ($unbindReturnContentAtt) {
            $syncState = self::syncBindRelationDeviceAndPage($pageIdAtt, array((int)$deviceId));
            return $syncState;
        }
        return false;
    }

    /**
     * 更新设备门店信息
     * @param $poiId int
     * @param $deviceIdAtt array
     * @param string $authAppId
     * @return bool|mixed
     */
    public static function updateDeviceLocationInfo($poiId, $deviceIdAtt, $authAppId = '')
    {
        $authAppId = $authAppId ?: session('user.app_id');
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
        foreach ($deviceIdAtt as $key => $deviceId) {
            $updateState = $shake->bindLocationDevice((int)$poiId, (int)$deviceId);
            if ($updateState === false) {
                unset($deviceIdAtt[$key]);
            }
        }
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($authAppId . 'ShakeDevice');
        $updateState = $shakeDeviceModel->updateDeviceInList($deviceIdAtt, array('poi_id' => $poiId));
        return $updateState;
    }

    /**
     * 更新设备门店信息
     * @param $comment string
     * @param $deviceIdAtt array
     * @param string $authAppId
     * @return bool|mixed
     */
    public static function updateDeviceCommentInfo($comment, $deviceIdAtt, $authAppId = '')
    {
        $authAppId = $authAppId ?: session('user.app_id');
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
        foreach ($deviceIdAtt as $key => $deviceId) {
            $updateState = $shake->updateDeviceComment($comment, (int)$deviceId);
            if ($updateState === false) {
                unset($deviceIdAtt[$key]);
            }
        }
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($authAppId . 'ShakeDevice');
        $updateState = $shakeDeviceModel->updateDeviceInList($deviceIdAtt, array('comment' => $comment));
        return $updateState;
    }

///用户授权后同步微信服务器数据部分
    /**
     * 同步设备列表（仅包含基础数据）
     * 该方法支持多次重复运行以完成任务。
     * @return bool 执行成功，返回true，失败返回false
     */
    public function syncDeviceList()
    {
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($this->authorizedAppId);
        if (!F(__METHOD__ . $_GET['AsyncTaskID'])) {
            $shakeDeviceModel->delAll();
            $searchDeviceCondition['begin'] = 0;
        } else {
            $count = $shakeDeviceModel->countNum();
            $searchDeviceCondition['begin'] = (int)$count;
        }
        F(__METHOD__ . $_GET['AsyncTaskID'], 'running');
        $searchDeviceCondition['type'] = 2;
        $searchDeviceCondition['count'] = 50;
        $addState = true;
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($this->authorizedAppId);
        $shakeDeviceInfoList = $shake->searchDevice($searchDeviceCondition);
        if (($shakeDeviceInfoList) && ($shakeDeviceInfoList['data']['devices'])) {
            $shakeDeviceAtt = $shakeDeviceInfoList['data']['devices'];
            $addState = $shakeDeviceModel->addAll($shakeDeviceAtt);
            if ($addState !== false) {
                $totalCount = $shakeDeviceInfoList['data']['total_count'];
                $getCount = $searchDeviceCondition['begin'] + $searchDeviceCondition['count'];
                while ($getCount < $totalCount) {
                    $searchDeviceCondition['begin'] = $getCount;
                    $getCount += $searchDeviceCondition['count'];
                    $shakeDeviceInfoList = $shake->searchDevice($searchDeviceCondition);
                    if ($shakeDeviceInfoList === false) {
                        break;
                    }
                    $shakeDeviceAtt = $shakeDeviceInfoList['data']['devices'];
                    $addState = $shakeDeviceModel->addAll($shakeDeviceAtt);
                    if ($addState === false) {
                        break;
                    }
                }
            }
        }
        if ($shakeDeviceInfoList && $addState) {
            F(__METHOD__ . $_GET['AsyncTaskID'], null);
            $taskInfo[] = array('url'
            => U("/Shake/Init/syncDeviceListRelationPage"
                    , array('appid' => $this->authorizedAppId),false));
            $taskInfo[] = array('url'
            => U("/Shake/Init/syncDeviceExtraTable"
                    , array('appid' => $this->authorizedAppId),false));
            $taskInfo[] = array('url'
            => U("/Shake/Init/syncDeviceListGroupList"
                    , array('appid' => $this->authorizedAppId),false));
            addTaskQueue($taskInfo);
            return true;
        } else {
            if ($addState === false) {
                errorLog('插入页面数据错误');
            } elseif ($shakeDeviceInfoList === false) {
                errorLog('查询设备列表不成功');
            }
            return false;
        }
    }

    /**
     * 设备列表中的绑定的页面信息
     * @return bool 成功返回true，失败返回false;
     */
    public function syncDeviceList_RelationPage()
    {
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($this->authorizedAppId);
        $deviceAtt = $shakeDeviceModel->selectNullPageDevices();
        if ($deviceAtt) {
            $deviceIdAtt = array();
            foreach ($deviceAtt as $device) {
                $deviceIdAtt[] = (int)$device['device_id'];
            }
            $this->syncDeviceBindRelation($deviceIdAtt, $this->authorizedAppId);
        }
        if ($deviceAtt !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 同步设备分组列表
     * @return bool 成功返回true，失败返回false;
     */
    public function syncDeviceList_GroupList()
    {
        $shakeDeviceGroupModel = new Weixin_Shake_DeviceGroupModel($this->authorizedAppId);
        if (!F(__METHOD__ . $_GET['AsyncTaskID'])) {
            $shakeDeviceGroupModel->delAll();
            $data['begin'] = 0;
        } else {
            $count = $shakeDeviceGroupModel->countNum();
            $data['begin'] = (int)$count;
        }
        F(__METHOD__ . $_GET['AsyncTaskID'], 'running');
        $data['count'] = 1000;
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($this->authorizedAppId);
        $groupList = $shake->getGroupList((int)$data['begin'], $data['count']);
        if ($groupList && $groupList['data']['groups']) {
            $addState = $shakeDeviceGroupModel->addAll($groupList['data']['groups']);
            if ($addState !== false) {
                $totalCount = $groupList['data']['total_count'];
                $getCount = $data['begin'] + $data['count'];
                while ($getCount < $totalCount) {
                    $data['begin'] = $getCount;
                    $getCount += $data['count'];
                    $groupList = $shake->getGroupList((int)$data['begin'], $data['count']);
                    if ($groupList === false) {
                        break;
                    }
                    $groupAtt = $groupList['data']['groups'];
                    $addState = $shakeDeviceGroupModel->addAll($groupAtt);
                    if ($addState === false) {
                        break;
                    }
                }
            } else {
                errorLog('数据库操作出现错误！');
                return false;
            }
        } elseif ($groupList === false) {
            errorLog('获取分组信息错误，$begin=' . $data['begin']);
            return false;
        }
        if ($groupList !== false) {
            F(__METHOD__ . $_GET['AsyncTaskID'], null);
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncDeviceListGroupListDetail"
                        , array('appid' => $this->authorizedAppId),false));
            addTaskQueue($taskInfo);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 将ShakeDeviceExtra中不存在的设备补齐
     * @return bool 成功返回true，失败返回false
     */
    public function syncDeviceExtraTable()
    {
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($this->authorizedAppId);
        $shakeDeviceExtraModel = new Weixin_Shake_DeviceExtraModel($this->authorizedAppId);
        $hadExistDeviceAtt = $shakeDeviceExtraModel->findMultiDevice(null, 'device_id');
        $hadExistDeviceIdAtt = '';
        if ($hadExistDeviceAtt) {
            foreach ($hadExistDeviceAtt as $device) {
                $hadExistDeviceIdAtt[] = $device['device_id'];
            }
        }
        $notExistDeviceAtt = $shakeDeviceModel->selectDeviceNotInList($hadExistDeviceIdAtt, 'device_id,major,minor');
        $addAllResult = true;
        if ($notExistDeviceAtt) {
            $addAllResult = $shakeDeviceExtraModel->addAll($notExistDeviceAtt);
        }
        if ($addAllResult !== false) {
            return true;
        } else {
            errorLog('同步' . $this->authorizedAppId . 'ShakeDeviceExtra' . '设备列表出错 addAllResult：' . $addAllResult);
            return false;
        }
    }

    /**
     * 同步分布列组中的详细信息，  同时也会更新设备列表中各设备的分组信息
     * @return bool
     */
    public function syncDeviceList_GroupList_Detail()
    {
        $shakeDeviceGroupModel = new Weixin_Shake_DeviceGroupModel($this->authorizedAppId);
        $shakeDeviceModel = new Weixin_Shake_DeviceModel($this->authorizedAppId);
        $groupAtt = $shakeDeviceGroupModel->selectNullDeviceGroup();
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($this->authorizedAppId);
        if ($groupAtt) {
            foreach ($groupAtt as $group) {
                $groupDetail = $shake->getGroupDetail((int)$group['group_id']);
                if ($groupDetail['data']['devices']) {
                    $device_ids = '';
                    foreach ($groupDetail['data']['devices'] as $deviceInfo) {
                        $device_ids .= ($deviceInfo['device_id'] . ',');
                    }
                    $device_ids = trim($device_ids, ',');
                    $saveState = $shakeDeviceModel
                        ->updateDeviceInList(explode(',', $device_ids),
                            array('group_id' => $group['group_id']));
                    if ($saveState === false) {
                        errorLog('更新设备分组信息失败！$groupId:' . $group['group_id']);
                        continue;
                    }
                    $saveState = $shakeDeviceGroupModel
                        ->updateGroup(array('group_id' => $group['group_id'])
                            , array('device_ids' => $device_ids));
                    if ($saveState === false) {
                        errorLog('插入分组设备列表失败！$groupId:' . $group['group_id']);
                        continue;
                    }
                }
            }
        }
        if ($groupAtt !== false) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *与微信服务器同步所有页面数据
     */
    public function syncPageList()
    {
        $shakePageModel = new Weixin_Shake_PageModel($this->authorizedAppId);
        if (!F(__METHOD__ . $_GET['AsyncTaskID'])) {
            $shakePageModel->delAll();
            $data['begin'] = 0;
        } else {
            $count = $shakePageModel->countNum();
            $data['begin'] = (int)$count;
        }
        F(__METHOD__ . $_GET['AsyncTaskID'], 'running');
        $data['type'] = 2;
        $data['count'] = 50;
        $addState = true;
        $shake = Weixin_ShakeAround_ShakeAround::getInstance($this->authorizedAppId);
        $shakePageInfoList = $shake->searchPage($data);
        if ($shakePageInfoList && $shakePageInfoList['data']['pages']) {
            $shakePageAtt = $shakePageInfoList['data']['pages'];
            $addState = $shakePageModel->addAll($shakePageAtt);
            if ($addState !== false) {
                $totalCount = $shakePageInfoList['data']['total_count'];
                $getCount = $data['begin'] + $data['count'];
                while ($getCount < $totalCount) {
                    $data['begin'] = $getCount;
                    $getCount += $data['count'];
                    $shakePageInfoList = $shake->searchPage($data);
                    if ($shakePageInfoList === false) {
                        break;
                    }
                    $shakePageAtt = $shakePageInfoList['data']['pages'];
                    $addState = $shakePageModel->addAll($shakePageAtt);
                    if ($addState === false) {
                        break;
                    }
                }
            }
        }
        if ($shakePageInfoList && $addState) {
            F(__METHOD__ . $_GET['AsyncTaskID'], null);
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncPageListRelationDevice"
                        , array('appid' => $this->authorizedAppId),false));
            $taskInfo[] = array(
                'url' =>
                    U("/Shake/Init/syncPageExtraTable"
                        , array('appid' => $this->authorizedAppId),false));
            addTaskQueue($taskInfo);
            return true;
        } else {
            if ($addState === false) {
                errorLog('插入页面数据错误');
            } elseif ($shakePageInfoList === false) {
                errorLog('查询页面列表不成功');
            }
            return false;
        }
    }

    /**
     * 将ShakePageExtra中不存在的设备补齐
     * @return bool 成功返回true，失败返回false
     */
    public function syncPageExtraTable()
    {
        $shakePageModel = new Weixin_Shake_PageModel($this->authorizedAppId);
        $shakePageExtraModel = new Weixin_Shake_PageExtraModel($this->authorizedAppId);
        $hadExistPageAtt = $shakePageExtraModel->findMultiPage(null, 'page_id');
        $hadExistPageIdAtt = [];
        if ($hadExistPageAtt) {
            foreach ($hadExistPageAtt as $page) {
                $hadExistPageIdAtt[] = $page['page_id'];
            }
        }
        $notExistPageAtt = $shakePageModel->selectPageNotInList($hadExistPageIdAtt, 'page_id');
        $addAllResult = true;
        if ($notExistPageAtt) {
            $addAllResult = $shakePageExtraModel->addAll($notExistPageAtt);
        }
        if ($addAllResult !== false) {
            return true;
        } else {
            errorLog('同步' . $this->authorizedAppId . '设备列表出错 addAllResult：' . $addAllResult);
            return false;
        }
    }

    /**
     * 同步页面列表绑定的设备信息
     * @return bool 成功返回true，失败返回false;
     */
    public
    function syncPageList_RelationDevice()
    {
        $shakePageExtraModel = new Weixin_Shake_PageExtraModel($this->authorizedAppId);
        $pageAtt = $shakePageExtraModel->selectNullDevicePage();
        if ($pageAtt) {
            $pageIdAtt = array();
            foreach ($pageAtt as $page) {
                $pageIdAtt[] = (int)$page['page_id'];
            }
            $this->syncPageBindRelation($pageIdAtt, $this->authorizedAppId);
        }
        if ($pageAtt !== false) {
            return true;
        } else {
            return false;
        }
    }
}