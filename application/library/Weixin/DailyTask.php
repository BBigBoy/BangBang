<?php
namespace Platform\Common;
load('Platform.WXOpenplatform');

/**
 * Created by PhpStorm.
 * User: bigbigboy
 * Date: 15-11-28
 * Time: 下午12:14
 */
class WXDailyTask
{

    /**
     *设定此方法定时任务，每日凌晨执行，判别授权公众号refresh_token是否快过期了
     * refresh_token有效期为30天,我们判断在25天左右刷新refresh_token
     */
    public function remainEffectiveAuthorizerRefreshToken()
    {
        $successNum = 0;
        $failNum = 0;
        $AuthorizerRefreshTokenModel = M('AccountAuthorizerInfo');
        $refresh_time = time() + 5 * 24 * 60 * 60;
        $map['componentappid'] = C('APP_ID');
        $map['authorizer_refresh_token_vld_timestamp'] = array('lt', $refresh_time);
        $authorizerList = $AuthorizerRefreshTokenModel->field('authorizerappid,authorizer_refresh_token,authorizer_refresh_token_vld_timestamp')
            ->where($map)->select();
        foreach ($authorizerList as $authorizer) {
            //用来刷新refresh_token
            $appid = C('APP_ID');
            $authorizerAppid = $authorizer['authorizerappid'];
            $authorizer_refresh_token = $authorizer['authorizer_refresh_token'];
            $postData = "{\"component_appid\":\"{$appid}\",\"authorizer_appid\":\"{$authorizerAppid}\",\"authorizer_refresh_token\":\"{$authorizer_refresh_token}\"}";
            $component_access_token = getComponent_Access_Token();
            $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;
            $reponseDataAtt = requestWXServer($postUrl, $postData);
            if ($reponseDataAtt) {
                $successNum++;
                $reponseDataAtt['authorizer_access_token_vld_timestamp'] = time() + 7000;
                $reponseDataAtt['authorizer_refresh_token_vld_timestamp'] = time() + 29 * 24 * 3600;
                $accountauthorizerinfo = M("AccountAuthorizerInfo"); // 实例化AccountAuthorizerInfo对象
                $whereAuthorizer['authorizerappid'] = $authorizerAppid;
                $whereAuthorizer['componentappid'] = C('APP_ID');
                $saveState = $accountauthorizerinfo->where($whereAuthorizer)->field('authorizer_access_token,authorizer_refresh_token,authorizer_access_token_vld_timestamp,authorizer_refresh_token_vld_timestamp')->save($reponseDataAtt); // 根据条件更新记录
                if ($saveState === false) {
                    $saveState = $accountauthorizerinfo->where($whereAuthorizer)->field('authorizer_access_token,authorizer_refresh_token,authorizer_access_token_vld_timestamp,authorizer_refresh_token_vld_timestamp')->save($reponseDataAtt); // 根据条件更新记录
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
            errorLog($failNum.'个公众号刷新口令更新失败', -3, true);
            return false;
        }
        echo true;
    }
}