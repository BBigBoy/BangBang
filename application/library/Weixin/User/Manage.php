<?php
class Weixin_User_Manage
{
    const USER_INFO_GET = 'https://api.weixin.qq.com/cgi-bin/user/info?';
    const MUTI_USER_INFO_GET = 'https://api.weixin.qq.com/cgi-bin/user/info/batchget?';
    const USER_LIST_GET = 'https://api.weixin.qq.com/cgi-bin/user/get?';
    /**
     * 获得用户信息
     * @param $auth_app_id string 公众账号APPID
     * @param $openid string 用户id
     * @return mixed
     */
    public function getUserInfo($auth_app_id, $openid)
    {
        if (!$openid) {
            errorLog('查询用户信息必须提供openid');
            return false;
        } elseif (!$auth_app_id) {
            errorLog('查询用户信息必须指定对应公众账号');
            return false;
        }
        $requestUrl = self::USER_INFO_GET . 'access_token=' . getAuthorizerAccessTokenByRefreshToken($auth_app_id) . '&openid=' . $openid . '&lang=zh_CN';
        $userInfo = requestWXServer($requestUrl);
        return $userInfo;
    }

    /**
     * 获得用户信息
     * @param $auth_app_id string 公众账号APPID
     * @param $openidAtt array 用户id
     * @return mixed
     */
    public function getMutiUserInfo($auth_app_id, $openidAtt)
    {
        if (!($openidAtt && is_array($openidAtt))) {
            errorLog('批量查询用户信息必须提供openid列表数组');
            return false;
        } elseif (!$auth_app_id) {
            errorLog('查询用户信息必须指定对应公众账号');
            return false;
        }
        $postData = array('user_list' => array());
        foreach ($openidAtt as $openid) {
            $postData['user_list'][] = array('openid' => $openid);
        }
        $requestUrl = self::MUTI_USER_INFO_GET . 'access_token=' . getAuthorizerAccessTokenByRefreshToken($auth_app_id);
        $userInfoList = requestWXServer($requestUrl, $postData);
        return $userInfoList;
    }

    /**
     * 拉取用户列表
     * @param $auth_app_id
     * @param string $startOpenId
     * @return mixed
     */
    public function getUserList($auth_app_id, $startOpenId = '')
    {
        if (!$auth_app_id) {
            errorLog('查询用户信息必须指定对应公众账号');
            return false;
        }
        $requestUrl = self::USER_LIST_GET . 'access_token=' . getAuthorizerAccessTokenByRefreshToken($auth_app_id);
        !$startOpenId ?: ($requestUrl .= ('&next_openid=' . $startOpenId));
        $userInfo = requestWXServer($requestUrl);
        return $userInfo;
    }
}