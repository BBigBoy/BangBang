<?php
class Weixin_OAuth_BaseUserOAuth extends Weixin_OAuth_UserOAuth
{
    /**
     * 登陆初始化，一般可以判断会话是否存在，如果已存在，则可以不再执行后续操作
     * @return bool 返回true或者false，
     * 返回true时，表示用户已登陆，不再进行后面的内容，直接返回
     * 返回false时，执行后续操作
     */
    function loginStart()
    {
        if (session('openid')) {
            return true;
        }
        return false;
    }

    /**
     * 每次用户登录第三方应用成功时都会调用此方法，可以记录用户的登录次数、事件等信息到用户表
     * @param $userInfo  array：
     *        当用户调用login方法时，传入true或者未传参，
     *         则默认获取用户信息，
     *        这个信息可能来自第三方引用自己保存的数据，
     *        也可能是从微信服务器拉取得用户基本信息
     *       当用户调用login方法时，传入false，则此处的$userInfo仅包含用户的openid     *
     * @return mixed
     */
    function loginSuccess($userInfo)
    {
        session('login_road', $userInfo['road']);
        session('openid', $userInfo['openid']);
        if ($userInfo['nickname']) {
            session('headImgUrl', $userInfo['headimgurl']);
            session('nickname', $userInfo['nickname']);
        }
    }

}