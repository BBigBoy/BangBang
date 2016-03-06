<?php
define('WXOpenplatform_APP_ID', C('APP_ID'));
define('WXOpenplatform_APP_SECRET', C('APP_SECRET'));

/************************************************************
 *********获得第三方开放平台口令及获取授权相关API************
 **********************************************************/
/**
 * 在公众号第三方平台创建审核通过后，微信服务器会向其“授权事件接收URL”每隔10分钟定时推送component_verify_ticket。
 * 在微信服务器推送ComponentVerifyTicket加密内容时，从中取出ComponentVerifyTicket
 * 这里从我们保存的缓存中取出ComponentVerifyTicket *
 * @return string ComponentVerifyTicket
 */
function getComponentVerifyTicket()
{
    $component_verify_ticket = F(WXOpenplatform_APP_ID . '/wx/component_verify_ticket');
    if ($component_verify_ticket) {
        return $component_verify_ticket;
    }
    errorLog('当前未保存到component_verify_ticket', -3, true);
    return false;
}

/**
 * 用于获取第三方平台令牌（component_access_token）
 */
function getComponent_Access_Token()
{
    ///缓存中获取
    $mem = S(WXOpenplatform_APP_ID . '/COMPONENT_ACCESS_TOKEN');
    if ($mem) {
        return $mem;
    }
    ///网络请求
    $component_verify_ticket = getComponentVerifyTicket();
    if ($component_verify_ticket === false) {
        errorLog('当前未保存到component_verify_ticket', -3, true);
        return false;
    }
    $postData = "{\"component_appid\":\"%s\",\"component_appsecret\":\"%s\",
        \"component_verify_ticket\":\"%s\"}";
    $postData = sprintf($postData, WXOpenplatform_APP_ID, WXOpenplatform_APP_SECRET, $component_verify_ticket);
    $reponseDataAtt = requestWXServer('https://api.weixin.qq.com/cgi-bin/component/api_component_token', $postData);
    if ($reponseDataAtt) {
        $component_access_token = $reponseDataAtt['component_access_token'];
        //(int)$jsonArr['expires_in']这个值为7200，为了避免在时间交叉点出现问题(如我的服务器与微信服务器时间存在不同不同步时)，将有限期设置为7000
        S(WXOpenplatform_APP_ID . '/COMPONENT_ACCESS_TOKEN', $component_access_token, 7000);
        return $component_access_token;
    } else {
        ///请求失败时再发起请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getComponent_Access_Token();
    }
}

/**
 * 用于获取预授权码。预授权码用于公众号授权时的第三方平台方安全验证。有效期为20分钟
 * @return string 返回预授权码
 */
function getPreAuthCode()
{
    $mem = S(WXOpenplatform_APP_ID . '/PRE_AUTH_CODE');
    if ($mem != false) {
        return $mem;
    }
    ///如果preauthcode.json文件不存在，或则preauthcode已经过期，则进行网络请求，获取新的preauthcode
    $app_id = WXOpenplatform_APP_ID;
    $postData = "{\"component_appid\":\"{$app_id}\"}";
    $component_access_token = getComponent_Access_Token();
    if ($component_access_token === false) {
        errorLog('COMPONENT_ACCESS_TOKEN获取失败', -3, true);
        return false;
    }
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token=' . $component_access_token;
    $reponseDataAtt = requestWXServer($postUrl, $postData);
    if ($reponseDataAtt) {
        $pre_auth_code = $reponseDataAtt['pre_auth_code'];
        //(int)$jsonArr['expires_in']这个值为600，为了避免在时间交叉点出现问题，将有限期设置为560
        S(WXOpenplatform_APP_ID . '/PRE_AUTH_CODE', $pre_auth_code, 560);
        return $pre_auth_code;
    } else {
        ///请求失败时再发起一次请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getPreAuthCode();
    }
}

/**
 * 使用授权码从微信服务器拉取公众号的授权信息（此方法一般在授权成功后立即调用，因此不做缓存优化）
 * @param $auth_code string 授权码，授权成功时返回
 * @return mixed 公众号授权信息
 */
function getAuthInfoByAuthCode($auth_code)
{
    ///参数校验
    if (!is_string($auth_code) || ($auth_code == '')) {
        errorLog('$auth_code错误');
        return false;
    }
    $appid = WXOpenplatform_APP_ID;
    $postData = "{\"component_appid\":\"{$appid}\",\"authorization_code\":\"{$auth_code}\"}";
    $component_access_token = getComponent_Access_Token();
    if ($component_access_token === false) {
        errorLog('COMPONENT_ACCESS_TOKEN获取失败', -3, true);
        return false;
    }
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token=' . $component_access_token;
    $reponseDataAtt = requestWXServer($postUrl, $postData);
    if ($reponseDataAtt) {
        $authorizerAppid = $reponseDataAtt['authorization_info']['authorizer_appid'];
        $authorizer_access_token = $reponseDataAtt['authorization_info']['authorizer_access_token'];
        $authorizer_refresh_token = $reponseDataAtt['authorization_info']['authorizer_refresh_token'];
        //$timestamp = time() + ((int)$jsonObj->authorization_info->expires_in);
        $timestamp = time() + 7000;
        $func_info = $reponseDataAtt['authorization_info']['func_info'];
        $func_list = '';
        foreach ($func_info as $value) {
            $func_list .= ($value['funcscope_category']['id'] . ';');
        }
        //保存到数据库
        $authInfo['authorizerappid'] = $authorizerAppid;
        $authInfo['componentappid'] = WXOpenplatform_APP_ID;
        $authInfo['authorizer_access_token'] = $authorizer_access_token;
        $authInfo['authorizer_refresh_token'] = $authorizer_refresh_token;
        $authInfo['authorizer_access_token_vld_timestamp'] = $timestamp;
        $authInfo['authorizer_refresh_token_vld_timestamp'] = time() + 29 * 24 * 3600;
        $authInfo['authorizer_funclist'] = $func_list;
        $accountAuthInfoModel = new Weixin_AccountAuthInfoModel(); // 实例化AccountAuthorizerInfo对象
        $accountAuthInfoModel->addAccount($authInfo);
        //添加缓存
        S(WXOpenplatform_APP_ID . '/AUTHORIZER_ACCESS_TOKEN-' . $authorizerAppid, $authorizer_access_token, 7000);
        return $authInfo;
    } else {
        errorLog('AuthInfoByAuthCode获取失败', -3, true);
        return false;
    }
}

/**
 * 用于在授权方令牌（authorizer_access_token）失效时，使用刷新令牌获得口令
 * 先从静态缓存中读取，缓存不存在则从数据库中读取，当$authorizer_access_token在有效期内时直接使用，不在有效期则使用刷新口令重新获取
 * @param $authorizerAppid string
 * @return mixed
 */
function getAuthorizerAccessTokenByRefreshToken($authorizerAppid)
{
    ///参数校验
    if (!is_string($authorizerAppid) || ($authorizerAppid == '')) {
        errorLog('$authorizerAppid错误');
        return false;
    }
    ///S缓存
    $mem = S(WXOpenplatform_APP_ID . '/AUTHORIZER_ACCESS_TOKEN-' . $authorizerAppid);
    if ($mem != false) {
        return $mem;
    }
    ///数据库缓存提取
    $whereAuthAccount['authorizerappid'] = $authorizerAppid;
    $whereAuthAccount['componentappid'] = WXOpenplatform_APP_ID;
    $accountInfoModel = new Weixin_AccountAuthInfoModel();
    $data = $accountInfoModel->findAccount($whereAuthAccount);
    $vld_timestamp = $data['authorizer_access_token_vld_timestamp'];
    $authorizer_access_token = $data['authorizer_access_token'];
    $authorizer_refresh_token = $data['authorizer_refresh_token'];
    if ($vld_timestamp > time()) {//数据库存储的授权令牌未过期
        return $authorizer_access_token;
    }
    ///网络获取
    $appid = WXOpenplatform_APP_ID;
    $postData = "{\"component_appid\":\"{$appid}\",\"authorizer_appid\":\"{$authorizerAppid}\",\"authorizer_refresh_token\":\"{$authorizer_refresh_token}\"}";
    $component_access_token = getComponent_Access_Token();
    if ($component_access_token === false) {
        errorLog('COMPONENT_ACCESS_TOKEN获取失败', -3, true);
        return false;
    }
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=' . $component_access_token;
    $authToken = requestWXServer($postUrl, $postData);
    if ($authToken) {
        $authToken['authorizer_access_token_vld_timestamp'] = time() + 7000;
        $authToken['authorizer_refresh_token_vld_timestamp'] = time() + 29 * 24 * 3600;
        unset($authToken['expires_in']);
        $accountInfoModel->updateAccount($authToken, $whereAuthAccount); // 根据条件更新记录
        S(WXOpenplatform_APP_ID . '/AUTHORIZER_ACCESS_TOKEN-' . $authorizerAppid, $authToken['authorizer_access_token'], 7000);
        return $authToken['authorizer_access_token'];
    } else {
        ///请求失败时再发起一次请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getAuthorizerAccessTokenByRefreshToken($authorizerAppid);
    }
}

/**
 * 从网络获取，因为这些信息可能发生变化。所以每隔一段时间都应通过网络获取最新信息。但是每次获取都应该更新数据库表
 * 用于获取授权方的公众号基本信息，包括头像、昵称、帐号类型、认证类型、微信号、原始ID和二维码图片URL。
 * 需要特别记录授权方的帐号类型，在消息及事件推送时，对于不具备客服接口的公众号，需要在5秒内立即响应；而若有客服接口，
 * 则可以选择暂时不响应，而选择后续通过客服接口来发送消息触达粉丝。
 * @param $componentAppid string 开放平台创建的APPID
 * @param $authAppid  string 授权方appid
 * @return mixed
 */
function getAuthorizerAccountInfo($componentAppid, $authAppid)
{
    ///参数校验
    if (!is_string($componentAppid) || ($componentAppid == '')) {
        errorLog('$componentAppid错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    $accountInfoModel = new Weixin_AuthorizedAccountInfoModel();
    $whereAuthorizer['authorizerappid'] = $authAppid;
    $whereAuthorizer['componentappid'] = $componentAppid;
    $accountInfoAtt = $accountInfoModel->findAccount($whereAuthorizer);
    if (!empty($accountInfoAtt)) {
        $vld_timestamp = $accountInfoAtt['vld_timestamp'];
        if ($vld_timestamp > time()) {
            $accountInfoObj = new Weixin_Account_Info();
            $accountInfoObj->constructFromAtt($accountInfoAtt);
            return $accountInfoObj;
        }
    }
    $postData = "{\"component_appid\":\"{$componentAppid}\",\"authorizer_appid\":\"{$authAppid}\"}";
    $component_access_token = getComponent_Access_Token();
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token=' . $component_access_token;
    $newAccountInfoAtt = requestWXServer($postUrl, $postData);
    if ($newAccountInfoAtt) {
        $vld_timestamp = time() + 60 * 60 * 6;//账户信息超过六个小时，即需要刷新
        $authorizerInfoAtt = $newAccountInfoAtt['authorizer_info'];
        $nick_name = $authorizerInfoAtt['nick_name'];
        $head_img_url = $authorizerInfoAtt['head_img'];
        $service_type_info = $authorizerInfoAtt['service_type_info']['id'];
        $verify_type_info = $authorizerInfoAtt['verify_type_info']['id'];
        $user_name = $authorizerInfoAtt['user_name'];
        $alias = $authorizerInfoAtt['alias'];
        $qrcode_url = $authorizerInfoAtt['qrcode_url'];
        $func_info = $authorizerInfoAtt['authorization_info']['func_info'];
        $func_list = '';
        foreach ($func_info as $i => $value) {
            $func_list .= ($value['funcscope_category']['id'] . ';');
        }
        $accountInfo['authorizerappid'] = $authAppid;
        $accountInfo['componentappid'] = $componentAppid;
        $accountInfo['vld_timestamp'] = $vld_timestamp;
        $accountInfo['nick_name'] = $nick_name;
        $accountInfo['head_img_url'] = $head_img_url;
        $accountInfo['service_type_info'] = $service_type_info;
        $accountInfo['verify_type_info'] = $verify_type_info;
        $accountInfo['user_name'] = $user_name;
        $accountInfo['alias'] = $alias;
        $accountInfo['qrcode_url'] = $qrcode_url;
        $accountInfo['funclist'] = $func_list;
        $accountInfoModel->addUser($accountInfo);
        $accountInfoObj = new Weixin_Account_Info();
        $accountInfoObj->constructFromAtt($accountInfo);
        return $accountInfoObj;
    } else {
        ///请求失败时再发起一次请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getAuthorizerAccountInfo($componentAppid, $authAppid);
    }
}

/**
 * 用于获取授权方的公众号的选项设置信息，如：地理位置上报，语音识别开关，多客服开关。注意，获取各项选项设置信息，需要有授权方的授权，详见权限集说明。
 * @param $componentAppid string 开放平台创建的APPID
 * @param $authAppid  string 授权方appid
 * @param $option_name  string 选项名称
 * @return mixed
 */
function getAuthorizerOptionInfo($componentAppid, $authAppid, $option_name)
{
    ///参数校验
    if (!is_string($componentAppid) || ($componentAppid == '')) {
        errorLog('$componentAppid错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    if (!is_string($option_name) || ($option_name == '')) {
        errorLog('$option_name错误');
        return false;
    }
    $postData = "{
                  \"component_appid\":\"{$componentAppid}\",
                  \"authorizer_appid\":\"{$authAppid}\"
                  \"option_name\":\"{$option_name}\"
                  }";
    $component_access_token = getComponent_Access_Token();
    if ($component_access_token === false) {
        errorLog('COMPONENT_ACCESS_TOKEN获取失败', -3, true);
        return false;
    }
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option?component_access_token=' . $component_access_token;
    $reponseData = requestWXServer($postUrl, $postData);
    if ($reponseData) {
        return $reponseData;
    } else {
        ///请求失败时再发起一次请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getAuthorizerOptionInfo($componentAppid, $authAppid, $option_name);
    }
}

/**
 * 用于设置授权方的公众号的选项信息，如：地理位置上报，语音识别开关，多客服开关。注意，设置各项选项设置信息，需要有授权方的授权，详见权限集说明。
 * @param $componentAppid string 开放平台创建的APPID
 * @param $authAppid  string 授权方appid
 * @param $option_name  string 选项名称
 * @param $option_value string 对应选项值
 * @return mixed
 */
function setAuthorizerOptionInfo($componentAppid, $authAppid, $option_name, $option_value)
{
    ///参数校验
    if (!is_string($componentAppid) || ($componentAppid == '')) {
        errorLog('$componentAppid错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    if (!is_string($option_name) || ($option_name == '')) {
        errorLog('$option_name错误');
        return false;
    }
    if (!is_string($option_value) || ($option_value == '')) {
        errorLog('$option_value错误');
        return false;
    }
    ///
    $postData = "{\"component_appid\":\"{$componentAppid}\",
                  \"authorizer_appid\":\"{$authAppid}\"
                  \"option_name\":\"{$option_name}\",
                  \"option_value\":\"{$option_value}\"}";
    $component_access_token = getComponent_Access_Token();
    if ($component_access_token === false) {
        errorLog('COMPONENT_ACCESS_TOKEN获取失败', -3, true);
        return false;
    }
    $postUrl = 'https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option?component_access_token=' . $component_access_token;
    $reponseData = requestWXServer($postUrl, $postData);
    if ($reponseData) {
        return $reponseData;
    } else {
        ///请求失败时再发起一次请求
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        $repeatNum++;
        getAuthorizerOptionInfo($componentAppid, $authAppid, $option_name);
    }
}

/************************************
 *********OAuth网页授权相关API************
 * 待进一步修改
 **********************************/
//公众号网页授权官方介绍http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html#.E9.99.84.EF.BC.9A.E6.A3.80.E9.AA.8C.E6.8E.88.E6.9D.83.E5.87.AD.E8.AF.81.EF.BC.88access_token.EF.BC.89.E6.98.AF.E5.90.A6.E6.9C.89.E6.95.88
//对公众号开发获取网页授权详细介绍博客http://www.cnblogs.com/txw1958/p/weixin71-oauth20.html


/**
 * 获得当前访问网页用户的授权信息,
 * 如果是授权获得用户信息，则需要将得到的accesstoken、refreshtoken
 * 及其过期时间保存到数据库中
 * @param $code string 由微信服务器在回调时返回
 * @param $authAppid string  由微信服务器在回调时返回,授权公众号的appid
 * @return mixed 未获取到授权信息则返回false，否则返回对应授权信息
 */
function getOAuthInfo($code, $authAppid)
{
    ///参数校验
    if (!is_string($code) || ($code == '')) {
        errorLog('code错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    $componentAccessToken = getComponent_Access_Token();
    if (!$componentAccessToken) {
        errorLog('Component_Access_Token获取失败', -3, true);
        return false;
    }
    $requestUrl = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid=%s&code=%s&grant_type=authorization_code&component_appid=%s&component_access_token=%s";
    $requestUrl = sprintf($requestUrl, $authAppid, $code, WXOpenplatform_APP_ID, $componentAccessToken);
    $oauthInfo = requestWXServer($requestUrl);
    unset($oauthInfo['expires_in']);
    if (!$oauthInfo) {
        errorLog(__FUNCTION__ . '获取失败', -3, true);
        return false;
    }
    if (strstr($oauthInfo['scope'], 'userinfo')) {
        ///仅当授权作用域为snsapi_userinfo获取用户信息的时候
        //才需要保存accesstoken和refreshtoken
        $oauthInfo['access_token_vldtimestamp'] = time() + 7000;
        $oauthInfo['refresh_token_vldtimestamp'] = time() + 29 * 24 * 3600;
        $whereFans['openid'] = $oauthInfo['openid'];
        $whereFans['authorizerappid'] = $authAppid;
        $whereFans['componentappid'] = WXOpenplatform_APP_ID;
        $fansTokenModel = new Weixin_FansTokenModel();
        $userInfo = $fansTokenModel->findFans($whereFans);
        if ($userInfo) {
            $fansTokenModel->updateFans($whereFans, $oauthInfo);
        } else {
            $oauthInfo['authorizerappid'] = $authAppid;
            $oauthInfo['componentappid'] = WXOpenplatform_APP_ID;
            $fansTokenModel->addFans($oauthInfo);
        }
    }
    return $oauthInfo;
}

/**
 *获取网页授权令牌，从数据库中查询，如果access_token仍在有效期，则直接返回，
 * 否则通过refresh_token刷新access_token令牌，并返回
 * 我们会每天凌晨执行定时任务，判断是否需要更新refresh_token
 * @param string $openid
 * @param $authAppid string 公众账号appid
 * @return mixed access_token
 */
function getOAuthAccessToken($openid, $authAppid)
{
    ///参数校验
    if (!is_string($openid) || ($openid == '')) {
        errorLog('openid错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    ///
    $whereFans['openid'] = $openid;
    $whereFans['authorizerappid'] = $authAppid;
    $whereFans['componentappid'] = WXOpenplatform_APP_ID;
    $whereFans['scope'] = 'snsapi_base,snsapi_userinfo,';
    $fansTokenModel = new Weixin_FansTokenModel();
    $fansInfo = $fansTokenModel->findFans($whereFans,
        'access_token,refresh_token,access_token_vldtimestamp');
    if ($fansInfo) {
        if ($fansInfo['access_token_vldtimestamp'] > time()) {
            return $fansInfo['access_token'];
        } else {
            //access_token已过期，重新刷新
            $access_token = getOAuthAccessToken_FromWXSever(
                $fansInfo['refresh_token'], $authAppid);
            if ($access_token) {
                return $access_token;
            }
        }
    }
    ///请求失败时的处理
    if (($fansInfo === false) || (isset($access_token) && ($access_token === false))) {
        static $repeatNum = 0;
        if ($repeatNum != 0) {//改变if条件就可以控制重复次数
            errorLog(__FUNCTION__ . '获取失败', -3, true);
            return false;
        }
        //避免第二次请求时，产生误判。
        unset($access_token);
        $repeatNum++;
        getOAuthAccessToken($openid, $authAppid);
    }
}

/**
 * 通过refresh_token重新获取access_token，同时refresh_token也会更新。
 * 所以本方法既可以用来刷新access_token，也可以更新refresh_token本身
 * @param $refresh_token string 网页授权刷新口令
 * @param $authAppid string 公众账号appid
 * @return mixed 成功返回access_token，失败返回false
 */
function getOAuthAccessToken_FromWXSever($refresh_token, $authAppid)
{
    ///参数校验
    if (!is_string($refresh_token) || ($refresh_token == '')) {
        errorLog('refresh_token错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    $componentAccessToken = getComponent_Access_Token();
    if (!$componentAccessToken) {
        errorLog('Component_Access_Token获取失败', -3, true);
        return false;
    }
    $requestUrl = "https://api.weixin.qq.com/sns/oauth2/component/refresh_token?appid=%s&grant_type=refresh_token&component_appid=%s&component_access_token=%s&refresh_token=%s";
    $requestUrl = sprintf($requestUrl, $authAppid, WXOpenplatform_APP_ID, $componentAccessToken, $refresh_token);
    $fansToken = requestWXServer($requestUrl);
    unset($fansToken['expires_in']);
    if (!$fansToken) {
        errorLog(__FUNCTION__ . '获取失败', -3, true);
        return false;
    }
    if (strstr($fansToken['scope'], 'userinfo')) {
        $fansToken['access_token_vldtimestamp'] = time() + 7000;
        $fansToken['refresh_token_vldtimestamp'] = time() + 30 * 24 * 3600;
        $whereFans['openid'] = $fansToken['openid'];
        $whereFans['authorizerappid'] = $authAppid;
        $whereFans['componentappid'] = WXOpenplatform_APP_ID;
        $fansTokenModel = new Weixin_FansTokenModel();
        $saveState = $fansTokenModel->updateFans($whereFans, $fansToken);
        if (!$saveState) {
            errorLog('OAuthAccessToken未成功保存至数据库，$responseAtt--->' . json_encode($fansToken), -3, true);
        }
    }
    return $fansToken['access_token'];
}

/**
 * 如果网页授权作用域为snsapi_userinfo，
 * 则此时开发者可以通过access_token和openid拉取用户信息了。
 * 当已经获取到用户信息了再调取此方法，则可以更新用户信息
 * @param $openid string 微信用户的openid
 * @param $authAppid string 公众账号appid
 * @param string $oauthAccessToken 网页授权令牌，
 *               建议当刚取得access_token时就自己传入，
 *               避免因为缓存原因取不到最新的令牌，造成难以发现的错误
 * @return mixed 授权用户信息
 */
function saveOAuthUserInfo($openid, $authAppid, $oauthAccessToken = '')
{
    ///参数校验
    if (!is_string($openid) || ($openid == '')) {
        errorLog('openid错误');
        return false;
    }
    if (!is_string($authAppid) || ($authAppid == '')) {
        errorLog('authAppid错误');
        return false;
    }
    if (!$oauthAccessToken) {
        //获取userinfo
        $oauthAccessToken = getOAuthAccessToken($openid, $authAppid);
        if (!$oauthAccessToken) {
            errorLog('当前不存在该用户的OAuthAccessToken', -3, true);
            return false;
        }
    }
    $requestUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=zh_CN";
    $requestUrl = sprintf($requestUrl, $oauthAccessToken, $openid);
    $fansInfo = requestWXServer($requestUrl);
    if ($fansInfo) {
        $fansInfo['privilege'] = json_encode($fansInfo['privilege']);
        $fansInfo['get_timestamp'] = time();
        $whereFans['authorizerappid'] = $authAppid;
        $whereFans['componentappid'] = WXOpenplatform_APP_ID;
        $whereFans['openid'] = $openid;
        $fansinfoModel = new Weixin_FansInfoModel(); // 实例化AccountAuthorizerInfo对象
        $updateState = $fansinfoModel->updateFans($whereFans, $fansInfo);
        if ($updateState === false) {
            errorLog('', -3, true);
            return false;
        } elseif ($updateState == 0) {
            $fans = $fansinfoModel->findFans($whereFans);
            if (!$fans) {
                $fansInfo['authorizerappid'] = $authAppid;
                $fansInfo['componentappid'] = WXOpenplatform_APP_ID;
                $addState = $fansinfoModel->addFans($fansInfo);
                if ($addState === false) {
                    errorLog('', -3, true);
                }
            }
        }
        return $fansInfo;
    }
}
