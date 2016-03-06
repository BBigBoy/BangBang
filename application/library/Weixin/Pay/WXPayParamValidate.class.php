<?php
namespace Platform\Common\WXPay;

/*
 * 对所需提交的参数做检查
 * 参数不合法，返回false
 * 参数合法，返回最终提交参数的xml格式
 */
class WXPayParamValidate{
    
    private $wxpaycommon;
    private $errorMsg;
    function __construct(){
        $this->wxpaycommon = new WXPayCommon();
        $this->errorMsg = "";
    }
    /*
     * 对企业付款的参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回最终所需提交的xml格式参数
     */
    public function validateTransfersParameters($parametersArray) {
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('mch_appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少mch_appid参数或mch_appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mchid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少mchid参数或mchid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少sign参数或sign参数值不合法<br>";
            }
            if (!array_key_exists('partner_trade_no', $parametersArray) ||
                !is_string($parametersArray['partner_trade_no']) ||
                strlen(trim($parametersArray['partner_trade_no'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少partner_trade_no参数或partner_trade_no参数值不合法<br>";
            }
            if (!array_key_exists('openid', $parametersArray) ||
                !is_string($parametersArray['openid']) ||
                strlen(trim($parametersArray['openid'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少openid参数或openid参数值不合法<br>";
            }
            if (!array_key_exists('check_name', $parametersArray) ||
                !is_string($parametersArray['check_name']) ||
                strlen(trim($parametersArray['check_name'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少check_name参数或check_name参数值不合法<br>";
            }
            if (!$this->checkIntParameter('amount', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少amount参数或amount参数值不合法<br>";
            }
            if (!array_key_exists('desc', $parametersArray) ||
                !is_string($parametersArray['desc']) ||
                strlen(trim($parametersArray['desc'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少desc参数或desc参数值不合法<br>";
            }
            if (!$this->checkStringParameter('spbill_create_ip', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少spbill_create_ip参数或spbill_create_ip参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                if ("NO_CHECK" != $parametersArray['check_name']){
                    if (!array_key_exists('re_user_name', $parametersArray) ||
                        !is_string($parametersArray['re_user_name']) ||
                        strlen(trim($parametersArray['re_user_name'])) <= 0){
                        $this->errorMsg .= date("Y-m-d H:i:s")."企业付款：缺少re_user_name参数或re_user_name参数值不合法<br>";
                        $this->wxpaycommon->logErrorMsg($this->errorMsg);
                        $this->errorMsg = "";
                        return false;
                    }
                }
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."企业付款：参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对查询企业付款参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回参数的xml格式
     */
    public function validateQueryTransfersParameters($parametersArray) {
        if (is_array($parametersArray)) {
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询企业付款：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询企业付款：缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('partner_trade_no', $parametersArray,28)) {
                $this->errorMsg .= date("Y-m-d H:i:s")."查询企业付款：缺少partner_trade_no参数或partner_trade_no参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询企业付款：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询企业付款：缺少appid参数或appid参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."查询企业付款：参数必需是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对现金红包的参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回参数的xml格式
     */
    public function validateRedpackParameters($parametersArray) {
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)) {
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_billno', $parametersArray, 28)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少mch_billno参数或mch_billno参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('wxappid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少wxappid参数或wxappid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('send_name', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少send_name参数或send_name参数值不合法<br>";
            }
            if (!$this->checkStringParameter('re_openid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少re_openid参数或re_openid参数值不合法<br>";
            }
            if (!$this->checkIntParameter('total_amount', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少total_amount参数或total_amount参数值不合法<br>";
            }
            if (!$this->checkIntParameter('total_num', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少total_num参数或total_num参数值不合法<br>";
            }
            if (!$this->checkStringParameter('wishing', $parametersArray, 128)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少wishing参数或wishing参数值不合法<br>";
            }
            if (!$this->checkStringParameter('client_ip', $parametersArray, 15)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少client_ip参数或client_ip参数值不合法<br>";
            }
            if (!$this->checkStringParameter('act_name', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少act_name参数或act_name参数值不合法<br>";
            }
            if (!$this->checkStringParameter('remark', $parametersArray, 256)){
                $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：缺少remark参数或remark参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                if (1 != intval($parametersArray['total_num'])){
                    $this->errorMsg .= date("Y-m-d H:i:s")."现金红包：total_num的值必须为1<br>";
                    $this->wxpaycommon->logErrorMsg($this->errorMsg);
                    $this->errorMsg = "";
                    return false;
                }
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."现金红包:参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对红包查询接口参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回参数的xml格式
     */
    public function validateQueryRedpackParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_billno', $parametersArray, 28)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少mch_billno参数或mch_billno参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('bill_type', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询现金红包：缺少bill_type参数或bill_type参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."红包查询：传入的参数类型必须为数组<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对裂变红包接口参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回参数的xml格式
     */
    public function validateGroupRedpackParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_billno', $parametersArray, 28)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少mch_billno参数或mch_billno参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('wxappid', $parametersArray)) {
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少wxappid参数或wxappid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('send_name', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少send_name参数或send_name参数值不合法<br>";
            }
            if (!$this->checkStringParameter('re_openid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少re_openid参数或re_openid参数值不合法<br>";
            }
            if (!$this->checkIntParameter('total_amount', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少total_amount参数或total_amount参数值不合法<br>";
            }
            if (!$this->checkIntParameter('total_num', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少total_num参数或total_num参数值不合法<br>";
            }
            if (!$this->checkStringParameter('amt_type', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少amt_type参数或amt_type参数值不合法<br>";
            }
            if (!$this->checkStringParameter('wishing', $parametersArray, 128)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少wishing参数或wishing参数值不合法<br>";
            }
            if (!$this->checkStringParameter('act_name', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少act_name参数或act_name参数值不合法<br>";
            }
            if (!$this->checkStringParameter('remark', $parametersArray, 256)){
                $this->errorMsg .= date("Y-m-d H:i:s")."裂变红包：缺少remark参数或remark参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."裂变红包：传入的参数类型必须为数组<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对代金券或立减优惠接口参数做检查
     * 如果参数不合法，则返回false
     * 如果参数合法，则返回参数的xml格式
     */
    public function validateCouponParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!array_key_exists('coupon_stock_id', $parametersArray) ||
                !is_string($parametersArray['coupon_stock_id']) ||
                strlen(trim($parametersArray['coupon_stock_id'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少coupon_stock_id参数或coupon_stock_id参数值不合法<br>";
            }
            if (!array_key_exists('openid_count', $parametersArray) ||
                !is_numeric($parametersArray['openid_count']) ||
                1 != intval($parametersArray['openid_count'])){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少openid_count参数或openid_count参数值不合法<br>";
            }
            if (!array_key_exists('partner_trade_no', $parametersArray) ||
                !is_string($parametersArray['partner_trade_no']) ||
                strlen(trim($parametersArray['partner_trade_no'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少partner_trade_no参数或partner_trade_no参数值不合法<br>";
            }
            if (!$this->checkStringParameter('openid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少openid参数或openid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：缺少sign参数或sign参数值不合法<br>";
            }
            if (array_key_exists('version', $parametersArray) && 
                "1.0" != $parametersArray['version']){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：version参数值不合法<br>";
            }
            if (array_key_exists('type', $parametersArray) &&
                "XML" != $parametersArray['type']){
                $this->errorMsg .= date("Y-m-d H:i:s")."代金券或立减优惠：type参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."代金券或立减优惠：传入的参数类型必须为数组<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            return false;
        }
    }
    /*
     * 对查询代金券批次参数做检查
     */
    public function validateQueryCouponParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!array_key_exists('coupon_stock_id', $parametersArray) ||
                !is_string($parametersArray['coupon_stock_id']) ||
                strlen(trim($parametersArray['coupon_stock_id'])) <= 0){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：缺少coupon_stock_id参数或coupon_stock_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：缺少sign参数或sign参数值不合法<br>";
            }
            if (array_key_exists('version', $parametersArray) &&
                "1.0" != $parametersArray['version']){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询代金券批次：version参数值不合法<br>";
            }
            if (array_key_exists('type', $parametersArray) &&
                "XML" != $parametersArray['type']){
                $this->errorMsg .= date("Y-m-d H:i:s")."type参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."查询代金券批次：传入的参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对统一下单接口参数作检查
     */
    public function validateUnifiedOrderParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('body', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少body参数或body参数值不合法<br>";
            }
            if (!$this->checkStringParameter('out_trade_no', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少out_trade_no参数或out_trade_no参数值不合法<br>";
            }
            if (!array_key_exists('total_fee', $parametersArray) ||
                !is_numeric($parametersArray['total_fee'])){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少total_fee参数或total_fee参数值不合法<br>";
            }
            if (!$this->checkStringParameter('spbill_create_ip', $parametersArray, 16)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少spbill_create_ip参数或spbill_create_ip参数值不合法<br>";
            }
            if (!$this->checkStringParameter('notify_url', $parametersArray, 256)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少notify_url参数或notify_url参数值不合法<br>";
            }
            if (!$this->checkStringParameter('trade_type', $parametersArray, 16)){
                $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少trade_type参数或trade_type参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                if ($parametersArray['trade_type'] == "JSAPI"){
                    if (!$this->checkStringParameter('openid', $parametersArray)){
                        $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少open参数或openid参数值不合法<br>";
                        $this->wxpaycommon->logErrorMsg($this->errorMsg);
                        $this->errorMsg = "";
                        return false;
                    }
                }elseif ($parametersArray['trade_type'] == "NATIVE"){
                    if (!$this->checkStringParameter('product_id', $parametersArray)){
                        $this->errorMsg .= date("Y-m-d H:i:s")."统一下单：缺少product_id参数或product_id参数值不合法<br>";
                        $this->wxpaycommon->logErrorMsg($this->errorMsg);
                        $this->errorMsg = "";
                        return false;
                    }
                }
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."统一下单：传入的参数必须是数组类型";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对查询订单参数做检查
     */
    public function validateQueryOrderParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:缺少sign参数或sign参数值不合法<br>";
            }
            if (array_key_exists('transaction_id', $parametersArray)){
                if (!$this->checkStringParameter('transaction_id', $parametersArray)){
                    $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:transaction_id参数值不合法<br>";
                }
            }
            if (array_key_exists('out_trade_no', $parametersArray)){
                if (!$this->checkStringParameter('out_trade_no', $parametersArray)){
                    $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:out_trade_no参数值不合法<br>";
                }
            }
            if (!array_key_exists('transaction_id', $parametersArray) &&
                !array_key_exists('out_trade_no', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询订单:transaction_id和out_trade_no两个必须填写一个<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."订单查询：传入的参数必须是数组类型";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对关闭订单参数做检查
     */
    public function validateCloseOrderParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."关闭订单：缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."关闭订单：缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."关闭订单：缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."关闭订单：缺少sign参数或sign参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."关闭订单：传入的参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对申请退款参数做检查
     */
    public function validateRefundParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('transaction_id', $parametersArray,28)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少transaction_id参数或transaction_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('out_trade_no', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少out_trade_no参数或out_trade_no参数值不合法<br>";
            }
            if (!$this->checkStringParameter('out_refund_no', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少out_refund_no参数或out_refund_no参数值不合法<br>";
            }
            if (!$this->checkIntParameter('total_fee', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少total_fee参数或total_fee参数值不合法<br>";
            }
            if (!$this->checkIntParameter('refund_fee', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少refund_fee参数或refund_fee参数值不合法<br>";
            }
            if (!$this->checkStringParameter('op_user_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."申请退款:缺少op_user_id参数或op_user_id参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."申请退款:传入的参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对查询退款参数做检查
     */
    public function validateQueryRefundParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询退款:缺少appid参数或appid参数值不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询退款:缺少mch_id参数或mch_id参数值不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询退款:缺少nonce_str参数或nonce_str参数值不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询退款:缺少sign参数或sign参数值不合法<br>";
            }
            if (!$this->checkStringParameter('out_trade_no', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."查询退款:缺少out_trade_no参数或out_trade_no参数值不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."查询退款:传入的参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对下载对账单接口参数做检查
     */
    public function validateDownloadBillParameters($parametersArray){
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."下载对账单:缺少appid参数或appid参数不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."下载对账单:缺少mch_id参数或mch_id参数不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."下载对账单:缺少nonce_str参数或nonce_str参数不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."下载对账单:缺少sign参数或sign参数不合法<br>";
            }
            if (!$this->checkStringParameter('bill_date', $parametersArray,8)){
                $this->errorMsg .= date("Y-m-d H:i:s")."下载对账单:缺少bill_date参数或bill_date参数不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else{
                $xml = $this->wxpaycommon->arrayToxml($parametersArray);
                return $xml;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."下载对账单:传入的参数必须 是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对原生支付模式一生成二维码所需的参数做检查
     * 参数合法返回参数的数组类型
     * 参数不合法返回false
     */
    public function validateNativeModelOneParameters($parametersArray) {
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appid', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少appid参数或appid参数不合法<br>";
            }
            if (!$this->checkStringParameter('mch_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少mch_id参数或mch_id参数不合法<br>";
            }
            if (!$this->checkStringParameter('time_stamp', $parametersArray,10)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少time_stamp参数或time_stamp参数不合法<br>";
            }
            if (!$this->checkStringParameter('nonce_str', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少nonce_str参数或nonce_str参数不合法<br>";
            }
            if (!$this->checkStringParameter('product_id', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少product_id参数或product_id参数不合法<br>";
            }
            if (!$this->checkStringParameter('sign', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."扫码支付模式一:缺少sign参数或sign参数不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                return $parametersArray;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."扫码支付模式一:传入的参数必须是数组类型<br>";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对jsapi网页内
     */
    public function validateJsapiParameters($parametersArray) {
        if (is_array($parametersArray)){
            if (!$this->checkStringParameter('appId', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少appId参数或appId参数不合法<br>";
            }
            if (!$this->checkStringParameter('timeStamp', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少timeStamp参数或timeStamp参数不合法<br>";
            }
            if (!$this->checkStringParameter('nonceStr', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少nonceStr参数或nonceStr参数不合法<br>";
            }
            if (!$this->checkStringParameter('package', $parametersArray, 128)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少package参数或package参数不合法<br>";
            }
            if (!$this->checkStringParameter('signType', $parametersArray)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少signType参数或signType参数不合法<br>";
            }
            if (!$this->checkStringParameter('paySign', $parametersArray, 64)){
                $this->errorMsg .= date("Y-m-d H:i:s")."jsapi:缺少paySign参数或paySign参数不合法<br>";
            }
            if ("" != $this->errorMsg){
                $this->wxpaycommon->logErrorMsg($this->errorMsg);
                $this->errorMsg = "";
                return false;
            }else {
                $json = json_encode($parametersArray);
                return $json;
            }
        }else {
            $this->errorMsg = date("Y-m-d H:i:s")."jsapi：传入的参数必须是数组类型";
            $this->wxpaycommon->logErrorMsg($this->errorMsg);
            $this->errorMsg = "";
            return false;
        }
    }
    /*
     * 对接口指定的字符串类型的参数做检查
     * @param param 对应接口指定的字符串参数的名称
     * @param parametersArray 对应接口指定的所有参数组成的数组
     * @param strlen 接口指定的param字符串最大长度
     * @return 数组中传进的与param对应的参数值，如果符合对应接口对指定参数的要求则返回true，否则返回false
     */
    private function checkStringParameter($param,$parametersArray,$strlen=32){
        if (!array_key_exists($param, $parametersArray) ||
            !is_string($parametersArray[$param]) ||
            strlen(trim($parametersArray[$param])) <= 0 ||
            strlen(trim($parametersArray[$param])) > $strlen){
            return false;
        }else{
            return true;
        }
    }
    /*
     * 描述：对金额口指定的整形类型的参数做检查
     * @param param 对应接口指定的字符串参数的名称
     * @param parametersArray 对应接口指定的所有参数组成的数组
     * @retrun 数组中传进的与param对应的参数值，如果符合对应接口对指定参数的要求则返回true，否则返回false
     */
    private function checkIntParameter($param,$parametersArray){
        if (!array_key_exists($param, $parametersArray) ||
            !is_numeric($parametersArray[$param]) ||
            strlen(trim($parametersArray[$param])) <= 0){
            return false;
        }else {
            return true;
        }
    }
}

?>