<?php

class EventController extends Own_Controller_Base
{
    public function indexAction()
    {
        exit('index</br>');
    }

    public function _empty()
    {
    }


    /**
     *处理微信服务器对开放平台的推送事件
     * 1、取消授权事件
     * 2、COMPONENT_VERIFY_TICKET推送事件
     */
    public function platformeventAction()
    {
        $msg_signature = getParam('get.msg_signature');
        $timeStamp = getParam('get.timestamp');
        $nonce = getParam('get.nonce');
        // 获得服务器POST过来的加密过的信息（包括取消授权通知与component_verify_ticket通知）
        $postStr = file_get_contents("php://input");
        $pc = new Weixin_Crypt_BizMsgCrypt(C('TOKEN'), C('ENCODING_AES_KEY'), C('APP_ID'));
        $msg = '';
        $errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $postStr, $msg, 'AppId');
        if ($errCode == 0) {
            $msgObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $infoType = (string)$msgObj->InfoType;
            if ($infoType == 'unauthorized') {
                $authorizerappid = (string)$msgObj->AuthorizerAppid;
                $whereAuthorizer['authorizerappid'] = $authorizerappid;
                $whereAuthorizer['componentappid'] = C('APP_ID');
                $accountauthorizerinfo = new Weixin_AccountAuthInfoModel();
                $accountauthorizerinfo->delAccount($whereAuthorizer);
            } else if ($infoType == 'component_verify_ticket') {
                $component_verify_ticket = (string)$msgObj->ComponentVerifyTicket;
                F(C('APP_ID') . '/wx/component_verify_ticket', $component_verify_ticket);
            } else {
                errorLog('openplatform_event1->msg:' . $msg, -3, true);
            }
        } else {
            errorLog('openplatform_event2->postStr:' . $postStr, -3, true);
        }
        echo 'success';
    }
}
