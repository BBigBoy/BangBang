<?php

class ChatController extends Own_Controller_Base
{
    /**
     *作为开放平台，微信会把授权给开放平台的公众号事件推送到本控制其对应的操作
     * 为了避免过多的操作方法，统一由这个空操作处理
     */
    public function callback()
    {
        $auth_app_id = getParam('get.appid');
        $msg_signature = getParam('get.msg_signature');
        $timeStamp = getParam('get.timestamp');
        $nonce = getParam('get.nonce');
        $postStr = file_get_contents("php://input");
        $pc = new Weixin_Crypt_BizMsgCrypt(C('TOKEN'), C('ENCODING_AES_KEY'), C('APP_ID'));
        $msg = '';
        $errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $postStr, $msg, 'AppId');
        if ($errCode === 0) {
            $wxPushArr = (array)simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($wxPushArr) {
                if ($auth_app_id == 'wx570bc396a51b8ff8') {
                    $this->dealWXVerify($wxPushArr);
                    exit;
                }
                if ($wxPushArr['MsgType'] == 'event') {
                    if ($wxPushArr['Event'] == 'ShakearoundUserShake') {
                        ///摇一摇事件推送
                        $this->updateShakeDisplayNum($auth_app_id, $wxPushArr);
                    } else if ($wxPushArr['Event'] == 'subscribe') {
                        //处理公众号关注事件
                        $this->saveSubscribeUserInfo($auth_app_id, $wxPushArr['FromUserName']);
                    } else if ($wxPushArr['Event'] == 'unsubscribe') {

                    }
                } elseif ($wxPushArr['MsgType'] == 'text') {
                    //处理用户发送的文字消息
                    $this->dealUserTextMsg($auth_app_id, $wxPushArr);
                }
            }
        }
        exit('success');
    }


    /**
     * 处理全网发布时微信开放平台的验证。   在完成全网发布以后，可以不运行这段代码
     * @param $wxPushArr array 微信开放平台推送的消息
     */
    private function dealWXVerify($wxPushArr)
    {
        $myWeChatObj = new Weixin_Chat_WeChat(array());
        if ($wxPushArr['MsgType'] == 'event') {
            $myWeChatObj->text($wxPushArr['Event'] . 'from_callback')->reply();
        } elseif ($wxPushArr['MsgType'] == 'text') {
            if (strpos($wxPushArr['Content'], 'QUERY_AUTH_CODE') !== false) {
                echo '';
                $query_auth_code = str_replace('QUERY_AUTH_CODE:', '', $wxPushArr['Content']);
                $authInfo = getAuthInfoByAuthCode($query_auth_code);
                $requestUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $authInfo['authorizer_access_token'];
                $data['touser'] = $wxPushArr['FromUserName'];
                $data['msgtype'] = 'text';
                $data['text'] = array('content' => ($query_auth_code . '_from_api'));
                errorLog(json_encode($data) . "\n" . $requestUrl);
                http_request($requestUrl, json_encode($data));
            } else {
                $myWeChatObj->text('TESTCOMPONENT_MSG_TYPE_TEXT_callback')->reply();
            }
        }

    }

    /**
     * 更新摇周边设备及其关联页面显示次数
     * @param $auth_app_id
     * @param $xmlObj mixed 微信服务器推送的xml对象
     */
    private function updateShakeDisplayNum($auth_app_id, $xmlObj)
    {
        $shakeInfo = (array)$xmlObj;
        $deviceExtraModel = new Weixin_Shake_DeviceExtraModel($auth_app_id);
        $aroundBeacons = (array)$shakeInfo['AroundBeacons'];
        if (count($aroundBeacons['AroundBeacon']) > 1) {
            $aroundBeacons['AroundBeacon'] = (array)$aroundBeacons['AroundBeacon'];
            foreach ($aroundBeacons['AroundBeacon'] as $aroundBeacon) {
                if (is_string($aroundBeacon)) {
                    $onlyOne = true;
                    $aroundBeacon = $aroundBeacons['AroundBeacon'];
                }
                $aroundBeacon = (array)$aroundBeacon;
                $condition['major'] = (int)$aroundBeacon['Major'];
                $condition['minor'] = (int)$aroundBeacon['Minor'];
                $result = $deviceExtraModel->incLiveNum($condition);
                if (!$result) {
                    errorLog($auth_app_id . '更新访问次数出错1' . gettype($aroundBeacon) . json_encode($aroundBeacon), -3, true);
                }
                if (isset($onlyOne) && $onlyOne) {
                    break;
                }
            }
        }
        $chosenBeacon = (array)$shakeInfo['ChosenBeacon'];
        $whereDevice['major'] = $chosenBeacon['Major'];
        $whereDevice['minor'] = $chosenBeacon['Minor'];
        $visitDevice['`visit_num`'] = array('exp', '`visit_num`+1');
        $visitDevice['`live_num`'] = array('exp', '`live_num`+1');
        $result = $deviceExtraModel->updateDeviceExtra($whereDevice, $visitDevice);
        //!$result包含两层意思，一个是访问数据库出错，另一个是更新的记录数为0
        //因为默认只要创建了设备，这张表里面一定存在该设备相关信息
        if (!$result) {
            errorLog($auth_app_id . '更新访问次数出错2' . json_encode($shakeInfo['ChosenBeacon']), -3, true);
        }

        $deviceModel = new Weixin_Shake_DeviceModel($auth_app_id);
        $deviceInfo = $deviceModel->findDevice($whereDevice, 'page_ids');
        if ($deviceInfo['page_ids']) {
            $pageIdAtt = explode(',', $deviceInfo['page_ids']);
            $shakePageModel = new Weixin_Shake_PageExtraModel($auth_app_id);
            $result = $shakePageModel->incDisplayNum($pageIdAtt);
            if ($result === false) {
                errorLog($auth_app_id . '更新访问次数出错3pageId-->' . $pageIdAtt, -3, true);
            }
        }
        exit('');
    }

    /**
     *公众号关注事件
     * @param $auth_app_id
     * @param $fromUserName mixed 微信服务器推送的xml对象
     */
    private function saveSubscribeUserInfo($auth_app_id, $fromUserName)
    {
        $userManager = new Weixin_Chat_UserManage();
        $userInfo = $userManager->getUserInfo($auth_app_id, $fromUserName);
        $userInfo['authorizerappid'] = $auth_app_id;
        $userInfo['componentappid'] = C('APP_ID');
        $focusFansInfo = new Weixin_FansInfoModel();
        $focusFansInfo->addFans($userInfo);
    }

    /**
     * 处理用户发送的文字消息，目前仅放到弹幕中
     * @param $auth_app_id
     * @param $xmlObj mixed 微信服务器推送的xml对象
     */
    private function dealUserTextMsg($auth_app_id, $xmlObj)
    {
        $barrage['barrage_text'] = $xmlObj['Content'];
        if (strtolower($barrage['barrage_text']) == 'wifi') {
            return;
        }
        $fromUserName = $xmlObj['FromUserName'];
        /* $focusFansInfo = M('FocusFansInfo');
         $field = 'nickname,headimgurl';
         $where['authorizerappid'] = $auth_app_id;
         $where['componentappid'] = C('APP_ID');
         $where['openid'] = $fromUserName;
         $userInfo = $focusFansInfo->where($where)->field($field)->find();
         */
        $userManager = new Weixin_Chat_UserManage();
        $userManager->getUserInfo($auth_app_id, $fromUserName);
        /*if ($barrage['barrage_text']) {
            $barrage['openid'] = $fromUserName;
            $barrage['head_image'] = $userInfo['headimgurl'];
            $barrage['nick_name'] = $userInfo['nickname'];
            $barrage['upload_time'] = time();
            $barrage['authorizerappid'] = $auth_app_id;
            $barrageTextModel = M('BarrageText');
            $barrageTextModel->add($barrage);
            S('last_update_time', time(), 7200);
        }*/
    }
}