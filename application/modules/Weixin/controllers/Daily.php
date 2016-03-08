<?php

class DailyController extends Own_Controller_Base
{

    /**
     *保持用户信息为最新，设定每天定时任务，更新用户信息。
     * 网页获取用户信息是没有接口频率限制的
     */
    function remainLatestUserInfoAction()
    {
        $startTime = time();
        $successNum = 0;
        $failNum = 0;
        $fansinfoModel = new Weixin_FansInfoModel();
        $userList = $fansinfoModel->findMultiFans(null, 'openid,authorizerappid');
        foreach ($userList as $uer) {
            $saveState = saveOAuthUserInfo($uer['openid'], $uer['authorizerappid']);
            if ($saveState) {
                $successNum++;
            } else {
                $failNum++;
            }
        }
        if ($failNum != 0) {
            errorLog(__FUNCTION__ . '出错啦', -3, true);
        }
        echo 'success:' . $successNum . '<->fail:' . $failNum . '<->startTime:' . $startTime . '<->endTime:' . time();
    }

    /**
     *设定此方法定时任务，每日凌晨执行，判别网页授权refresh_token是否快过期了
     * refresh_token有效期为30天,我们判断在25天左右刷新refresh_token
     */
    function remainEffectiveOAuthRefreshTokenAction()
    {
        $startTime = time();
        $successNum = 0;
        $failNum = 0;
        $refresh_time = time() + 5 * 24 * 60 * 60;
        $map['componentappid'] = C('APP_ID');
        $map['refresh_token_vldtimestamp'] = array('lt', $refresh_time);
        $OAuthFansTokenModel = new Weixin_FansTokenModel();
        $oAuthFansList = $OAuthFansTokenModel
            ->findFans($map, 'authorizerappid,refresh_token,refresh_token_vldtimestamp');
        foreach ($oAuthFansList as $oAuthFans) {
            //用来刷新refresh_token
            $oauthAccessToken = getOAuthAccessTokenFromWXSever($oAuthFans['refresh_token'], $oAuthFans['authorizerappid']);
            if ($oauthAccessToken === false) {
                $failNum++;
            } else {
                $successNum++;
            }
        }
        if ($failNum != 0) {
            errorLog(__FUNCTION__ . '出错啦', -3, true);
        }
        echo 'success:' . $successNum . '<->fail:' . $failNum . '<->startTime:' . $startTime . '<->endTime:' . time() . '<->timeLength:' . (time() - $startTime);
    }

    /**
     *设定此方法定时任务，每日凌晨执行，判别授权公众号refresh_token是否快过期了
     * refresh_token有效期为30天,我们判断在25天左右刷新refresh_token
     */
    function remainEffectiveAuthorizerRefreshTokenAction()
    {
        $startTime = time();
        $successNum = 0;
        $failNum = 0;
        $refresh_time = time() + 5 * 24 * 60 * 60;
        $map['componentappid'] = C('APP_ID');
        $map['authorizer_refresh_token_vld_timestamp'] = array('lt', $refresh_time);
        $accountAuthInfoModel = new Weixin_AccountAuthInfoModel();
        $authorizerList = $accountAuthInfoModel
            ->findMultiAccount($map,
                'authorizerappid,authorizer_refresh_token,authorizer_refresh_token_vld_timestamp');
        foreach ($authorizerList as $authorizer) {
            //用来刷新refresh_token
            $appid = C('APP_ID');
            $authorizerAppid = $authorizer['authorizerappid'];
            $authorizer_refresh_token = $authorizer['authorizer_refresh_token'];
            $postData = "{\"component_appid\":\"{$appid}\",\"authorizer_appid\":\"{$authorizerAppid}\",\"authorizer_refresh_token\":\"{$authorizer_refresh_token}\"}";
            $component_access_token = getComponentAccessToken();
            $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;
            $authInfo = requestWXServer($postUrl, $postData);
            unset($authInfo['expires_in']);
            if ($authInfo) {
                $successNum++;
                $authInfo['authorizer_access_token_vld_timestamp'] = time() + 7000;
                $authInfo['authorizer_refresh_token_vld_timestamp'] = time() + 29 * 24 * 3600;
                $whereAuthorizer['authorizerappid'] = $authorizerAppid;
                $whereAuthorizer['componentappid'] = C('APP_ID');
                $saveState = $accountAuthInfoModel
                    ->updateAccount($whereAuthorizer, $authInfo);
                if ($saveState === false) {
                    $saveState = $accountAuthInfoModel
                        ->updateAccount($whereAuthorizer, $authInfo);
                    if ($saveState === false) {
                        errorLog(__FUNCTION__ . '获取失败', -3, true);
                        $failNum++;
                    }
                }
            } else {
                errorLog(__FUNCTION__ . '获取失败', -3, true);
                $failNum++;
            }
        }
        if ($failNum != 0) {
            errorLog(__FUNCTION__ . '出错啦', -3, true);
        }
        echo 'success:' . $successNum . '<->fail:' . $failNum . '<->startTime:' . $startTime . '<->endTime:' . time();
    }

}