<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付公共方法类
 */
class WXPayCommon{
    /*
     * 企业付款生成商户订单号
     * 商户号+年月日+当前时间戳
     */
    public function createPartnerTradeNo(){
        $partner_trade_no = "";
        $partner_trade_no = WXPayConf::MCHID.date("Ymd").time();
        return $partner_trade_no;
    }
    /*
     * 随机字符串生成函数
     */
    public function createNonceStr(){
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $len = rand(20, 32);
        $nonce_str = "";
        for ($i = 0; $i < $len; $i++){
            $position = rand(0, 61);
            $nonce_str .= $str[$position];
        }
        return $nonce_str;
    }
    /*
     * 字符串处理函数
     */
    public function trimString($string){
        $result = null;
        if (null != $string){
            $result = trim($string);
            if (strlen($result) == 0){
                $result = null;
            }
        }
        return $result;
    }
    /*
     * 生成签名函数
     * 参数值为空的的参数不参与签名
     */
    public function createSign($parameters){
        $param = array();
        foreach ($parameters as $key => $value){
            if (null != $this->trimString($value)){
                $param[$key] = $value;
            }
        }
        ksort($param);
        $stringA = "";
        foreach ($param as $k => $v){
            $stringA .= $k."=".$v."&";
        }
        $stringSignTemp = $stringA."key=".WXPayConf::KEY;
        $sign = md5($stringSignTemp);
        $signValue = strtoupper($sign);
        return $signValue;
    }
    /*
     * 把数组转换为xml格式
     */
    public function arrayToxml($array){
        $xml = "<xml>";
        foreach ($array as $key => $value){
            if (is_numeric($value)){
                $xml .= "<".$key.">".$value."</".$key.">";
            }else {
                $xml .= "<".$key."><![CDATA[".$value."]]></".$key.">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }
    /*
     * 把xml数据转换成数组格式
     */
    public function xmlToArray($xml){
        $array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array;
    }
    /*
     * 以post方式带证书提交参数到企业付款查询接口
     */
    public function postxmlSSLCurl($xml,$url,$second=30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, WXPayConf::SSLCERT_PATH);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, WXPayConf::SSLKEY_PATH);
        curl_setopt($ch, CURLOPT_CAINFO, WXPayConf::SSLCA_PATH);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $data = curl_exec($ch);
        if ($data){
            curl_close($ch);
            return $data;
        }else {
            $error = curl_errno($ch);
            echo "curl出错，错误码：$error<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }
    /*
     * 以post方式不带证书提交xml数据到统一下单接口url
     */
    public function postXmlCurl($xml,$url,$second=30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);
        if($result)
        {
            curl_close($ch);
            return $result;
        }
        else
        {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }
    /*
     * 记录出错信息方法
     */
    public function logErrorMsg($errorMsg){
        file_put_contents('log.txt', $errorMsg);
    }
    /*
     * 现金红包商户订单号
     */
    public function createMchBillno(){
        $mch_billno = WXPayConf::MCHID.date("YmdHis").rand(1000, 9999);
        return $mch_billno;
    }
}

?>