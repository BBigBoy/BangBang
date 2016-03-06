<?php
/**
 * Created by PhpStorm.
 * User: BigBigBoy
 * Date: 2015/8/25
 * Time: 16:53
 */
class Weixin_Tools_Tools
{

    /**
     *创建关注公众账号的二维码
     * 输出的是二维码包含的信息
     * @param $authAppId string 授权的公众账号名称
     * @return mixed  可用于生成二维码关注公众账号的链接，错误返回false
     */
    public function createQRCode($authAppId)
    {
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . getAuthorizerAccessTokenByRefreshToken($authAppId);
        $postData = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 1}}}';
        $returnContent = requestWXServer($postUrl, $postData);
        if (is_array($returnContent)) {
            return $returnContent['url'];
        } else {
            errorLog('');
            return false;
        }
    }
}