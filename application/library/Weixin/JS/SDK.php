<?php

class Weixin_JS_SDK
{
    private $authorizerAppId;
    private $appid;

    //private $appSecret;

    public function __construct($authorizerAppId)
    {
        $this->authorizerAppId = $authorizerAppId;
        //$this->appSecret = $appSecret;
        $this->appid = C('APP_ID');
    }

    public function getSignPackage()
    {
        $jsapiTicket = $this->getJsApiTicket($this->authorizerAppId);
        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->authorizerAppId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    private function getJsApiTicket($authorizerAppid)
    {
        $whereAuthorizer['authorizerappid'] = $authorizerAppid;
        $whereAuthorizer['componentappid'] = $this->appid;
        $ticketModel = new Weixin_JSTicketModel();
        $data = $ticketModel->findAuthorizer($whereAuthorizer);
        if (!is_null($data)) {
            $vld_timestamp = $data['vld_timestamp'];
            $jsapi_ticket = $data['jsapi_ticket'];
            if ($vld_timestamp > time()) {//数据库存储的授权令牌已过期
                return $jsapi_ticket;
            }
        }
        $authorizerAccessToken = getAuthorizerAccessTokenByRefreshToken($authorizerAppid);
        // 如果是企业号用以下 URL 获取 ticket
        // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$authorizerAccessToken";
        $res = requestWXServer($url);
        $jsapi_ticket = $res['ticket'];
        $vld_timestamp = time() + ((int)$res['expires_in']);
        $ticketData['authorizerappid'] = $authorizerAppid;
        $ticketData['componentappid'] = $this->appid;
        $ticketData['jsapi_ticket'] = $jsapi_ticket;
        $ticketData['vld_timestamp'] = $vld_timestamp;
        if (is_null($data)) {
            $ticketModel->addTicketInfo($ticketData);
        } else {
            $ticketModel->updateTiketInfo($ticketData, $whereAuthorizer);
        }
        return $jsapi_ticket;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

}

