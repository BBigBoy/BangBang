<?php

/**
 * Class UserOAuth
 * @package Platform\Common\WXOAuth
 */
abstract class Weixin_OAuth_UserOAuth
{
    /**
     *用户登录，创建会话，记录登录信息。
     * 支持通过cookie、摇一摇、OAuth三种方式获取访问用户openid
     * @param $needUserInfo bool 标识是否需要获得用户信息,默认需要
     * @param $authAppId string 用户登录时绑定的微信公众号的APPID，
     *                          如果用户没有传入指定APPID，
     *                          则默认为当前应用绑定的APPID；
     *                          如果当前应用未绑定微信公众号，
     *                          则登陆将直接返回，不能成功登陆
     * @return bool false表示登陆失败，true表示成功
     */
    public function login($needUserInfo = true, $authAppId = '')
    {
        if ($this->loginStart()) {
            return true;
        }
        if (!$authAppId) {
            $authAppId = C('AUTH_APP_ID');
        }
        if (!$authAppId) {
            ///如果当前应用未配置绑定的公众号APPID，则登陆失败
            return false;
        }
        ///获取用户openid。
        $openid = '';
        $oAuthInfo = '';//用于保存通过网页授权登陆时的授权信息
        $loginRoad = '';//记录登陆的方式，cookie，还是授权，还是摇一摇
        if ((!$needUserInfo || ($needUserInfo && cookie('hadUserInfo'))) && cookie($authAppId . 'openid')) {
            //如果存在cookie,则直接使用cookie。  因为cookie存在的时候，账户信息已经获取到了
            $openid = cookie($authAppId . 'openid');
            $loginRoad = 'cookie';
        } elseif (I('get.ticket')) {
            //用户摇一摇时将通过本条件
            $shakeAround = Weixin_ShakeAround_ShakeAround::getInstance($authAppId);
            $userShakeInfo = $shakeAround->getUserShakeInfo(I('get.ticket'));
            if ($userShakeInfo['data']['openid']) {
                $openid = $userShakeInfo['data']['openid'];
                $loginRoad = 'shake';
                $this->loginByShake_Extra($userShakeInfo);
            } else {
                $this->redirectForOpenid($authAppId);
            }
        } elseif ($_GET['code'] && $_GET['appid']) {
            if (S($_GET['code'])) {
                //因为微信oauth认证的时候可能会重复定向两次
                //errorLog('拒绝重复code');
                S($_GET['code'], null);
                exit;
            }
            S($_GET['code'], 1, 300);
            //用户通过OAuth授权时将使用本方法
            $oAuthInfo = getOAuthInfo($_GET['code'], $authAppId);
            $openid = $oAuthInfo['openid'];
            if (!$openid) {
                $this->redirectForOpenid($authAppId);
            }
            $loginRoad = 'oauth';
            $this->loginByOAuth_Extra($openid);
        } else {
            $this->redirectForOpenid($authAppId);
        }
        ///程序执行到这里就一定有openid了，此后查询数据库
        if ($needUserInfo) {
            ///获取用户基本信息
            if (strstr($oAuthInfo['scope'], 'userinfo')) {
                //授权作用域为获取用户基本信息时，保存用户信息至平台数据库，
                //并调用firstLogin方法
                $oAuthUserInfo = saveOAuthUserInfo($openid, $authAppId, $oAuthInfo['access_token']);
                //这里是为了后面调用loginUserInfo方法时考虑，
                //数据刚插入数据库时可能无法马上查询到，因此使用缓存解决
                S($oAuthUserInfo['openid'] . 'forLoginUserInfo', json_encode($oAuthUserInfo), 10);
                if ($oAuthUserInfo['openid'])
                    $this->firstLogin($oAuthUserInfo);
            }
            //如果子类覆写了本方法，将调用子类指定的第三方数据库保存的信息
            $userInfo = $this->loginUserInfo($openid);
            if (!$userInfo) {
                //如果第三方应用没有该用户信息，则检测平台是否拥有该用户的信息
                //如果平台也没有该用户信息，则发起网页授权认证
                $userInfo = Weixin_OAuth_UserOAuth::loginUserInfo($openid);
                if ($userInfo) {
                    $this->firstLogin($userInfo);
                } else {
                    $this->redirectForUserInfo($authAppId);
                }
            }
            ///程序执行到这里一定已经获得用户信息
            cookie('hadUserInfo', 'yes');
        }
        if (!isset($userInfo['openid'])) {
            $userInfo['openid'] = $openid;
        }
        $userInfo['road'] = $loginRoad;
        //登陆成功
        $this->loginSuccess($userInfo);
        cookie($authAppId . 'openid', $openid);
        return true;
    }

    /**
     * 在每次登陆开始的时候执行。
     * 可以做一些登陆的准备工作，但是基本没什么可做的。
     * 一个重要的作用是判断是否需要执行后面的登录工作，
     * 一个可行的的办法是，在每次登陆成功loginSuccess时创建一个session会话，
     * 然后在本方法中判断该会话是否存在，如果已存在则不再进行后面的登录，
     * 这在网站存在页面跳转或刷新时特别有用
     * @return bool 返回true或者false，
     * 返回true时，表示用户已登陆，不再进行后面的内容，直接返回，login方法也会返回true
     * 返回false时，执行后续登陆操作
     */
    abstract function loginStart();

    /**
     * 通过摇一摇方式进入时额外需要做的工作，默认为空
     * @param $userShakeInfo array 摇一摇时通过ticket换取的设备及用户信息
     */
    protected function loginByShake_Extra($userShakeInfo)
    {
    }

    /**
     *重定向当前页面以获取用户openid
     * @param $authAppId string 用户登录时绑定的微信公众号的APPID
     * 如果第三方应用当前已配置绑定的公众账号，则不必传入此参数
     * @param string $redrictUrl 需要获得用户openid的回调地址，默认为访问脚本源地址
     */
    public static function redirectForOpenid($authAppId = '', $redrictUrl = '')
    {
        if (!$authAppId) {
            $authAppId = C('AUTH_APP_ID');
        }
        if (!$redrictUrl)
            $redrictUrl = urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        $scope = 'snsapi_base';
        $state = 'base';
        $baseHeaderUrl = "Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s&component_appid=%s#wechat_redirect";
        $headerUrl = sprintf($baseHeaderUrl, $authAppId, $redrictUrl, $scope, $state, C('APP_ID'));
        header($headerUrl);
        exit;
    }

    /**
     * 通过网页授权认证进入的额外操作，默认为空
     * @param $openid string
     */
    protected function loginByOAuth_Extra($openid)
    {
    }

    /**
     * 当该用户第一次登陆第三方应用时将调起此方法，
     * 一般是保存用户信息到自己的数据表
     * 需要注意的是，如果你采用的是分布式数据库，或者数据库写入时间较长，
     * 此时你应该同时使用缓存保存用户信息，以使得loginUserInfo能够取得用户信息
     * @param $userInfoAtt array 包含从微信获取的所有用户信息
     * @return mixed
     */
    protected function firstLogin($userInfoAtt)
    {
    }

    /**
     * 获得登录用户信息,默认从平台粉丝数据库获取（用户头像链接、昵称），第三方应用可覆盖本方法，返回
     * 第三方数据库存储的信息
     * @param $openid
     * @return mixed 返回第三方应用数据库表中查到的用户信息
     */
    protected function loginUserInfo($openid)
    {
        if (S($openid . 'forLoginUserInfo')) {
            return json_decode(S($openid . 'forLoginUserInfo'), true);
        }
        $fields = array(
            "nickname",
            "sex",
            "province",
            "city",
            'country',
            'headimgurl',
            'openid',
            'get_timestamp');
        $whereFans['openid'] = $openid;
        $fansModel = new Weixin_FansInfoModel();
        $fansInfo = $fansModel->findFans($whereFans, implode(',', $fields));
        return $fansInfo;
    }

    /**
     * 重定向以获取用户基础信息
     * @param $authAppId string 用户登录时绑定的微信公众号的APPID
     * 如果第三方应用当前已配置绑定的公众账号，则不必传入此参数
     * @param string $redrictUrl 需要获得用户openid的回调地址，默认为访问脚本源地址
     */
    public static function redirectForUserInfo($authAppId = '', $redrictUrl = '')
    {
        if (!$authAppId) {
            $authAppId = C('AUTH_APP_ID');
        }
        if (!$redrictUrl)
            $redrictUrl = urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
        $scope = 'snsapi_userinfo';
        $state = 'userinfo';
        $baseHeaderUrl = "Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s&component_appid=%s#wechat_redirect";
        $headerUrl = sprintf($baseHeaderUrl, $authAppId, $redrictUrl, $scope, $state, C('APP_ID'));
        header($headerUrl);
        exit;
    }

    /**
     * 每次用户登录第三方应用成功时都会调用此方法，
     * 此处可以记录用户的登录次数、事件等信息到用户表
     * @param $userInfo array：
     *        当用户调用login方法时，传入true或者未0    传参，
     *        这个信息可能来自第三方引用自己保存的数据，
     *        也可能是从微信服务器拉取得用户基本信息
     *       当用户调用login方法时，传入false，则此处的$userInfo仅包含用户的openid     *
     * @return mixed
     */
    abstract function loginSuccess($userInfo);

    /**
     * 获取最新的用户信息
     * 用户可选择在恰当的时机调用本方法更新所保存的用户信息
     * 如果第三方应用未覆盖本类中loginUserInfo方法，则可以
     * 根据其返回信息中的get_time字段来选择更新的时机。
     * （get_time字段是用户信息最后更新时间）
     * 不建议第三方应用频繁集中（一秒内调用多次）调用本方法，
     * 因为本方法需要通过http请求调用微信接口，比较耗时耗资源
     * @param $openid string 用户ID
     * @param $authAppId string 用户登录时绑定的微信公众号的APPID
     * 如果第三方应用当前已配置绑定的公众账号，则不必传入此参数
     * @return mixed
     */
    public function getLatestUserInfo($openid, $authAppId = '')
    {
        if (!$authAppId) {
            $authAppId = C('AUTH_APP_ID');
        }
        //saveOAuthUserInfo内部已经对传入参数进行了校验，
        //此处不再进行参数合法性校验
        $userInfo = saveOAuthUserInfo($openid, $authAppId);
        return $userInfo;
    }

}