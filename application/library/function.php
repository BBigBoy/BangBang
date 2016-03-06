<?php

/**
 * http请求，根据$data是否为空，自动进行get或post操作
 * @param $url
 * @param null $data
 * @param int $curl_time 允许curl函数执行的时间，控制它可以实现异步http请求
 * @return mixed
 */
function http_request($url, $data = null, $curl_time = 0)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if ($data != null) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    if ($curl_time > 0) {
        curl_setopt($curl, CURLOPT_NOSIGNAL, 1);    //注意，毫秒超时一定要设置这个
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, $curl_time);  //超时毫秒，cURL 7.16.2中被加入。从PHP 5.2.3起可使用
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    if (curl_errno($curl)) {//出错则显示错误信息
        print curl_error($curl);
    }
    curl_close($curl);
    return $output;
}

/**
 * 异步http请求，不等待返回结果
 * 根据$data是否为空，自动进行get或post操作
 * @param $url
 * @param null $data
 * @return mixed
 */
function async_http_request_no_result($url, $data = null)
{
    http_request($url, $data, 1);
}

/**
 * 向微信服务器请求数据，并处理返回结果
 *
 * @param $post_url string 具体请求的API地址
 * @param $data mixed 向微信服务器提交的数据,可以是json形式的，也可以是数组
 * @param $is_file bool 标识当前调用是否为文件上传，默认为false
 * @return bool|mixed  为NULL或者错误代码，则返回false。  若为有效值则直接返回结果数组。
 */
function requestWXServer($post_url, $data = null, $is_file = false)
{
    //当第一次请求失败时，递归请求一次，仍然失败则返回false
    static $requestTimes = 0;
    if (is_array($data) && !$is_file)
        $data = decodeUnicodeToUTF8(json_encode($data));
    $wxResponseStr = http_request($post_url, $data);
    if ($wxResponseStr) {
        $wxResponseAtt = json_decode($wxResponseStr, true);
        //(__METHOD__, __LINE__, json_encode($wxResponseAtt), $wxResponseAtt['errcode']);
        if (!$wxResponseAtt || (((int)($wxResponseAtt['errcode'])) !== 0)) {
            if ($requestTimes === 0 && (((int)($wxResponseAtt['errcode'])) == -1)) {
                errorLog('$requestTimes--->' . $requestTimes . '---posturl---->' . $post_url . '---postdata---->' . json_encode($data) . '---wxResponseStr---->' . $wxResponseStr, $wxResponseAtt['errcode']);
                $requestTimes++;
                requestWXServer($post_url, $data);
            } else {
                if ($wxResponseAtt['errcode'] != 9001003)
                    errorLog('$requestTimes--->' . $requestTimes . '---posturl---->' . $post_url . '---postdata---->' . json_encode($data) . '---wxResponseStr---->' . $wxResponseStr, $wxResponseAtt['errcode']);
                return false;
            }
        }
        return $wxResponseAtt;
    }
    //第一次请求出错，则再请求一次，如果仍然出错，则返回false
    if ($requestTimes === 0) {
        errorLog('$requestTimes--->' . $requestTimes . '---posturl---->' . $post_url . '---postdata---->' . json_encode($data));
        $requestTimes++;
        requestWXServer($post_url, $data);
    } else {
        errorLog('$requestTimes--->' . $requestTimes . '---posturl---->' . $post_url . '---postdata---->' . json_encode($data));
        return false;
    }
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}
/**
 * 将unicode格式的字符串转换为UTF-8
 * @param $str
 * @return mixed
 */
function decodeUnicodeToUTF8($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}

/**
 * 与errorLog类似，都是记录错误日志，
 * 但是这个方法用来在调式的时候使用，测试一些情况下的状态 *
 * @param string $addMsg 附加的错误信息
 * @param int $errorCode 错误编号
 */
function checkLog($addMsg = '', $errorCode = -3)
{
    $errorModel = M('CheckError');
    $errorInfo['errBatch'] = NOW_TIME;
    $errorInfo['module'] = MODULE_NAME;
    $errorInfo['controller'] = CONTROLLER_NAME;
    $errorInfo['action'] = ACTION_NAME;
    $debugBacktrace = debug_backtrace();
    $invokeMethodInfo = $debugBacktrace[1];
    $errorInfo['method'] = $invokeMethodInfo['class'] . '::' . $invokeMethodInfo['function'];
    $errorInfo['line'] = $debugBacktrace[0]['line'];
    $errorInfo['errcode'] = $errorCode ? $errorCode : -3;
    $errorInfo['errmsg'] = $addMsg;
    $requestContent = 'clientIP:' . get_client_ip() . '--->post参数：' . decodeUnicodeToUTF8(json_encode(I('post.'))) . '<--->get参数:' . $_SERVER['REQUEST_URI'];
    $errorInfo['requestContent'] = stripslashes($requestContent);
    $errorInfo['backtrace'] = stripslashes(decodeUnicodeToUTF8(json_encode(debug_backtrace())));
    $errorModel->add($errorInfo);
}

/**
 * 记录程序错误信息
 * @param string $addMsg 附加的错误信息
 * @param int $errorCode 错误编号
 * @param bool|false $mail 是否发送邮件提醒
 */
function errorLog($addMsg = '', $errorCode = -3, $mail = false)
{
    $errorModel = M('InterfaceError');
    $errorInfo['errBatch'] = NOW_TIME;
    $errorInfo['module'] = MODULE_NAME;
    $errorInfo['controller'] = CONTROLLER_NAME;
    $errorInfo['action'] = ACTION_NAME;
    $debugBacktrace = debug_backtrace();
    $invokeMethodInfo = $debugBacktrace[1];
    $errorInfo['method'] = $invokeMethodInfo['class'] . '::' . $invokeMethodInfo['function'];
    $errorInfo['line'] = $debugBacktrace[0]['line'];
    $errorInfo['errcode'] = $errorCode ? $errorCode : -3;
    $errorInfo['errmsg'] = $addMsg;
    $requestContent = 'clientIP:' . get_client_ip() . '--->post参数：' . decodeUnicodeToUTF8(json_encode(I('post.'))) . '<--->get参数:' . $_SERVER['REQUEST_URI'];
    $errorInfo['requestContent'] = stripslashes($requestContent);
    $errorInfo['backtrace'] = stripslashes(decodeUnicodeToUTF8(json_encode(debug_backtrace())));
    $errorModel->add($errorInfo);
    if ($mail) {
        $errMsg = json_encode($errorInfo);
        sendMail('928056199@qq.com', '错误提示', stripslashes(decodeUnicodeToUTF8($errMsg)));
    }
}

/**
 * 压缩html : 清除换行符,清除制表符,去掉注释标记
 * @param $string
 * @return string 压缩后的$string
 * */
function compress_html($string)
{
    $string = str_replace("\r\n", '', $string); //清除换行符
    $string = str_replace("\n", '', $string); //清除换行符
    $string = str_replace("\t", '', $string); //清除制表符
    $pattern = array(
        "/> *([^ ]*) *</", //去掉注释标记
        "/[\s]+/",
        "/<!--[^!]*-->/",
        "/\" /",
        "/ \"/",
        "'/\*[^*]*\*/'"
    );
    $replace = array(
        ">\\1<",
        " ",
        "",
        "\"",
        "\"",
        ""
    );
    return preg_replace($pattern, $replace, $string);

}


/**
 * 获得字符串长度，英文字符长为1，中文字符长度为2
 * @param $string string 待计算长度的字符串
 * @return int 字符串长度
 */
function stringLength($string)
{
    $string = (string)$string;
    return (strlen($string) + mb_strlen($string, 'utf-8')) / 2;
}

/**
 * 将数组转换为url的get参数形式的字符串
 * @param $arr array 需要转换的数组
 * @param bool $questionOrAndMark 前面添加'?'号或者'&'号
 * @return string 返回拼接后的字符串
 */
function arrToGetParamStr($arr, $questionOrAndMark = true)
{
    $getString = $questionOrAndMark ? '?' : '&';
    foreach ($arr as $key => &$value) {
        $value = ($key . '=' . $value);
    }
    $getString .= implode('&', $arr);
    return $getString;
}

/**
 * 获得当前访问网站的移动设备类型
 * @return string
 */
function getMobileInfo()
{
    $useragent = strtolower($_SERVER["HTTP_USER_AGENT"]);
    // iphone
    $is_iphone = strripos($useragent, 'iphone');
    if ($is_iphone) {
        return 'iphone';
    }
    // android
    $is_android = strripos($useragent, 'android');
    if ($is_android) {
        return 'android';
    }
    // 微信
    /*$is_weixin = strripos($useragent, 'micromessenger');
    if ($is_weixin) {
        return 'weixin';
    }*/
    // ipad
    $is_ipad = strripos($useragent, 'ipad');
    if ($is_ipad) {
        return 'ipad';
    }
    // ipod
    $is_ipod = strripos($useragent, 'ipod');
    if ($is_ipod) {
        return 'ipod';
    }
    //windows phone
    $is_windows = strripos($useragent, 'windows phone');
    if ($is_windows) {
        return 'windows phone';
    }
    // pc电脑
    /*$is_pc = strripos($useragent, 'windows nt');
    if ($is_pc) {
        return 'pc';
    }*/
    return 'other';
}