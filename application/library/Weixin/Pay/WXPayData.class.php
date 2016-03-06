<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付构建参数类
 * 可以直接传入一个包含所有必需参数的数组对参数进行构造
 * 也可以通过设置单个键值对对参数进行构造
 */
class WXPayData{
    /*
     * 公共数组参数
     */
    private $parametersArray = array();
    /*
     * 获取参数
     */
    public function getParametersArray()
    {
        return $this->parametersArray;
    }

    /*
     * 返回单个键所对应的值
     */
    public function getParameter($key)
    {
        return $this->parametersArray[$key];
    }
    /*
     * 通过传入数组构建参数
     */
    public function setParametersArray($parametersArray)
    {
        $this->parametersArray = $parametersArray;
    }

    /*
     * 设置单个键值对参数
     */
    public function setParameter($key,$value)
    {
        $this->parametersArray[$key] = $value;
    }
    /*
     * 生成随机字符串
     */
    public function setNonce_Str($nonceStrKey){
        $this->parametersArray[$nonceStrKey] = $this->createNonceStr();
    }
    /*
     * 签名
     */
    public function setSign($signkey){
        $sign = $this->createSign($this->getParametersArray());
        $this->parametersArray[$signkey] = $sign;
    }
    /*
     * 字符串处理函数
     */
    private function trimString($string){
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
    private function createSign($parameters){
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
        //echo "stringA:".$stringA.'<br>';
        $stringSignTemp = $stringA."key=".WXPayConf::KEY;
        //echo "stringSignTemp:".$stringSignTemp.'<br>';
        $sign = md5($stringSignTemp);
        //echo "sign:".$sign.'<br>';
        $signValue = strtoupper($sign);
        //echo "signValue:".$signValue.'<br>';
        return $signValue;
    }
    /*
     * 随机字符串生成函数
     */
    private function createNonceStr(){
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $len = rand(20, 32);
        $nonce_str = "";
        for ($i = 0; $i < $len; $i++){
            $position = rand(0, 61);
            $nonce_str .= $str[$position];
        }
        return $nonce_str;
    }
}
?>