<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付各个接口所对应的参数构建类
 * 1、可以通过直接传入一个数组构建参数
 * 2、可以通过设置各个参数项一步一步的构建参数
 */
class WXPayAPIData
{
    /*
     * 微信支付各接口api参数
     */
    private $transfersParameter = array();//企业付款
    private $queryTransfersInfoParameter = array();//查询企业付款
    private $sendRedpackParameter = array();//现金红包
    private $queryRedpackInfoParameter = array();//查询现金红包
    private $sendGroupRedpackParameter = array();//裂变红包
    private $sendCouponParameter = array();//代金券与立减折扣
    private $queryCouponStockParameter = array();//查询代金券批次
    /**
     * @return the $transfersParameter
    */
    public function getTransfersParameter(){
        return $this->transfersParameter;
    }
    /**
     * @return the $queryTransfersInfoParameter
     */
    public function getQueryTransfersInfoParameter(){
        return $this->queryTransfersInfoParameter;
    }
    /**
     * @return the $sendRedpackParameter
     */
    public function getSendRedpackParameter(){
        return $this->sendRedpackParameter;
    }
    /**
     * @return the $queryRedpackInfoParameter
     */
    public function getQueryRedpackInfoParameter(){
        return $this->queryRedpackInfoParameter;
    }
    /**
     * @return the $sendGroupRedpackParameter
     */
    public function getSendGroupRedpackParameter(){
        return $this->sendGroupRedpackParameter;
    }
    /**
     * @return the $sendCouponParameter
     */
    public function getSendCouponParameter(){
        return $this->sendCouponParameter;
    }
    /**
     * @return the $queryCouponStockParameter
     */
    public function getQueryCouponStockParameter(){
        return $this->queryCouponStockParameter;
    }
    /**
     * @param multitype: $transfersParameter
     */
    public function setTransfersParameter($transfersParameter){
        $this->transfersParameter = $transfersParameter;
    }
    /**
     * @param multitype: $queryTransfersInfoParameter
     */
    public function setQueryTransfersInfoParameter($queryTransfersInfoParameter){
        $this->queryTransfersInfoParameter = $queryTransfersInfoParameter;
    }
    /**
     * @param multitype: $sendRedpackParameter
     */
    public function setSendRedpackParameter($sendRedpackParameter){
        $this->sendRedpackParameter = $sendRedpackParameter;
    }
    /**
     * @param multitype: $queryRedpackInfoParameter
     */
    public function setQueryRedpackInfoParameter($queryRedpackInfoParameter){
        $this->queryRedpackInfoParameter = $queryRedpackInfoParameter;
    }
    /**
     * @param multitype: $sendGroupRedpackParameter
     */
    public function setSendGroupRedpackParameter($sendGroupRedpackParameter){
        $this->sendGroupRedpackParameter = $sendGroupRedpackParameter;
    }
    /**
     * @param multitype: $sendCouponParameter
     */
    public function setSendCouponParameter($sendCouponParameter){
        $this->sendCouponParameter = $sendCouponParameter;
    }
    /**
     * @param multitype: $queryCouponStockParameter
     */
    public function setQueryCouponStockParameter($queryCouponStockParameter){
        $this->queryCouponStockParameter = $queryCouponStockParameter;
    }
    /*
     * 设置企业付款接口参数
     */
    public function setTransfersParamMchAppid($mch_appid){
        $this->transfersParameter['mch_appid'] = $mch_appid;
    }
    public function setTransfersParamMchid($mchid) {
        $this->transfersParameter['mchid'] = $mchid;
    }
    public function setTransfersParamDeviceInfo($device_info){
        $this->transfersParameter['device_info'] = $device_info;
    }
    public function setTransfersParamNonceStr(){
        if (func_num_args() == 0){
            $this->transfersParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->transfersParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setTransfersParamSign(){
        $sign = $this->createSign($this->getTransfersParameter());
        $this->transfersParameter['sign'] = $sign;
    }
    public function setTransfersParamPartnerTradeNo($partner_trade_no){
        $this->transfersParameter['partner_trade_no'] = $partner_trade_no;
    }
    public function setTransfersParamOpenid($openid){
        $this->transfersParameter['openid'] = $openid;
    }
    public function setTransfersParamCheckName($check_name){
        $this->transfersParameter['check_name'] = $check_name;
    }
    public function setTransfersParamReUserName($re_user_name){
        $this->transfersParameter['re_user_name'] = $re_user_name;
    }
    public function setTransfersParamAmount($amount){
        $this->transfersParameter['amount'] = $amount;
    }
    public function setTransfersParamDesc($desc){
        $this->transfersParameter['desc'] = $desc;
    }
    public function setTransfersParamSpbillCreateIp($spbill_create_ip){
        $this->transfersParameter['spbill_create_ip'] = $spbill_create_ip;
    }
    /*
     * 设置查询企业付款接口参数
     */
    public function setQueryTransfersParamNonceStr(){
        if (func_num_args() == 0){
            $this->queryTransfersInfoParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->queryTransfersInfoParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setQueryTransfersParamSign(){
        $sign = $this->createSign($this->getQueryTransfersInfoParameter());
        $this->queryTransfersInfoParameter['sign'] = $sign;
    }
    public function setQueryTransfersParamPartnerTradeNo($partner_trade_no){
        $this->queryTransfersInfoParameter['partner_trade_no'] = $partner_trade_no;
    }
    public function setQueryTransfersParamMchId($mch_id){
        $this->queryTransfersInfoParameter['mch_id'] = $mch_id;
    }
    public function setQueryTransfersParamAppid($appid){
        $this->queryTransfersInfoParameter['appid'] = $appid;
    }
    /*
     * 设置现金红包接口参数
     */
    public function setSendRedpackParamNonceStr(){
        if (func_num_args() == 0){
            $this->sendRedpackParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->sendRedpackParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setSendRedpackParamSign(){
        $sign = $this->createSign($this->getSendRedpackParameter());
        $this->sendRedpackParameter['sign'] = $sign;
    }
    public function setSendRedpackParamMchBillno($mch_billno){
        $this->sendRedpackParameter['mch_billno'] = $mch_billno;
    }
    public function setSendRedpackParamMchId($mch_id){
        $this->sendRedpackParameter['mch_id'] = $mch_id;
    }
    public function setSendRedpackParamWxappid($wxappid){
        $this->sendRedpackParameter['wxappid'] = $wxappid;
    }
    public function setSendRedpackParamNickName($nick_name){
        $this->sendRedpackParameter['nick_name'] = $nick_name;
    }
    public function setSendRedpackParamSendName($send_name){
        $this->sendRedpackParameter['send_name'] = $send_name;
    }
    public function setSendRedpackParamReOpenid($re_openid){
        $this->sendRedpackParameter['re_openid'] = $re_openid;
    }
    public function setSendRedpackParamTotalAmount($total_amount){
        $this->sendRedpackParameter['total_amount'] = $total_amount;
    }
    public function setSendRedpackParamMinValue($min_value){
        $this->sendRedpackParameter['min_value'] = $min_value;
    }
    public function setSendRedpackParamMaxValue($max_value){
        $this->sendRedpackParameter['max_value'] = $max_value;
    }
    public function setSendRedpackParamTotalNum($total_num){
        $this->sendRedpackParameter['total_num'] = $total_num;
    }
    public function setSendRedpackParamWishing($wishing){
        $this->sendRedpackParameter['wishing'] = $wishing;
    }
    public function setSendRedpackParamClientIp($client_ip){
        $this->sendRedpackParameter['client_ip'] = $client_ip;
    }
    public function setSendRedpackParamActName($act_name){
        $this->sendRedpackParameter['act_name']  = $act_name;
    }
    public function setSendRedpackParamRemark($remark){
        $this->sendRedpackParameter['remark'] = $remark;
    }
    public function setSendRedpackParamLogoImgurl($logo_imgurl){
        $this->sendRedpackParameter['logo_imgurl'] = $logo_imgurl;
    }
    /*
     * 设置查询红包接口参数
     */
    public function setQueryRedpackInfoParamNonceStr(){
        if (func_num_args() == 0){
            $this->queryRedpackInfoParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->queryRedpackInfoParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setQueryRedpackInfoParamSign(){
        $sign = $this->createSign($this->getQueryRedpackInfoParameter());
        $this->queryRedpackInfoParameter['sign'] = $sign;
    }
    public function setQueryRedpackInfoParamMchBillno($mch_billno){
        $this->queryRedpackInfoParameter['mch_billno'] = $mch_billno;
    }
    public function setQueryRedpackInfoParamMchId($mch_id){
        $this->queryRedpackInfoParameter['mch_id'] = $mch_id;
    }
    public function setQueryRedpackInfoParamAppid($appid){
        $this->queryRedpackInfoParameter['appid'] = $appid;
    }
    public function setQueryRedpackInfoParamBillType($bill_type){
        $this->queryRedpackInfoParameter['bill_type'] = $bill_type;
    }
    /*
     * 设置裂变红包接口参数
     */
    public function setSendGroupRedpackParamNonceStr(){
        if (func_num_args() == 0){
            $this->sendGroupRedpackParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->sendGroupRedpackParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setSendGroupRedpackParamSign(){
        $sign = $this->createSign($this->getSendGroupRedpackParameter());
        $this->sendGroupRedpackParameter['sign'] = $sign;
    }
    public function setSendGroupRedpackParamMchBillno($mch_billno){
        $this->sendGroupRedpackParameter['mch_billno'] = $mch_billno;
    }
    public function setSendGroupRedpackParamMchId($mch_id){
        $this->sendGroupRedpackParameter['mch_id'] = $mch_id;
    }
    public function setSendGroupRedpackParamWxappid($wxappid){
        $this->sendGroupRedpackParameter['wxappid'] = $wxappid;
    }
    public function setSendGroupRedpackParamSendName($send_name){
        $this->sendGroupRedpackParameter['send_name'] = $send_name;
    }
    public function setSendGroupRedpackParamReOpenid($re_openid){
        $this->sendGroupRedpackParameter['re_openid'] = $re_openid;
    }
    public function setSendGroupRedpackParamTotalAmount($total_amount){
        $this->sendGroupRedpackParameter['total_amount'] = $total_amount;
    }
    public function setSendGroupRedpackParamTotalNum($total_num){
        $this->sendGroupRedpackParameter['total_num'] = $total_num;
    }
    public function setSendGroupRedpackParamAmtType($amt_type){
        $this->sendGroupRedpackParameter['amt_type'] = $amt_type;
    }
    public function setSendGroupRedpackParamAmtList($amt_list){
        $this->sendGroupRedpackParameter['amt_list'] = $amt_list;
    }
    public function setSendGroupRedpackParamWishing($wishing){
        $this->sendGroupRedpackParameter['wishing'] = $wishing;
    }
    public function setSendGroupRedpackParamActName($act_name){
        $this->sendGroupRedpackParameter['act_name'] = $act_name;
    }
    public function setSendGroupRedpackParamRemark($remark){
        $this->sendGroupRedpackParameter['remark'] = $remark;
    }
    /*
     * 设置发放代金券批次接口参数
     */
    public function setSendCouponParamCouponStockId($coupon_stock_id){
        $this->sendCouponParameter['coupon_stock_id'] = $coupon_stock_id;
    }
    public function setSendCouponParamOpenidCount($openid_count){
        $this->sendCouponParameter['openid_count'] = $openid_count;
    }
    public function setSendCouponParamPartnerTradeNo($partner_trade_no){
        $this->sendCouponParameter['partner_trade_no'] = $partner_trade_no;
    }
    public function setSendCouponParamOpenid($openid){
        $this->sendCouponParameter['openid'] = $openid;
    }
    public function setSendCouponParamAppid($appid){
        $this->sendCouponParameter['appid'] = $appid;
    }
    public function setSendCouponParamMchId($mch_id){
        $this->sendCouponParameter['mch_id'] = $mch_id;
    }
    public function setSendCouponParamOpUserId($op_user_id){
        $this->sendCouponParameter['op_user_id'] = $op_user_id;
    }
    public function setSendCouponParamDeviceInfo($device_info){
        $this->sendCouponParameter['device_info'] = $device_info;
    }
    public function setSendCouponParamNonceStr(){
        if (func_num_args() == 0){
            $this->sendCouponParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->sendCouponParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setSendCouponParamSign(){
        $sign = $this->createSign($this->getSendCouponParameter());
        $this->sendCouponParameter['sign'] = $sign;
    }
    public function setSendCouponParamVersion($version){
        $this->sendCouponParameter['version'] = $version;
    }
    public function setSendCouponParamType($type){
        $this->sendCouponParameter['type'] = $type;
    }
    /*
     * 设置查询代金券批次接口参数
     */
    public function setQueryCouponStockParamCouponStockId($coupon_stock_id){
        $this->queryCouponStockParameter['coupon_stock_id'] = $coupon_stock_id;
    }
    public function setQueryCouponStockParamAppid($appid){
        $this->queryCouponStockParameter['appid'] = $appid;
    }
    public function setQueryCouponStockParamMchId($mch_id){
        $this->queryCouponStockParameter['mch_id'] = $mch_id;
    }
    public function setQueryCouponStockParamOpUserId($op_user_id){
        $this->queryCouponStockParameter['op_user_id'] = $op_user_id;
    }
    public function setQueryCouponStockParamDeviceInfo($device_info){
        $this->queryCouponStockParameter['device_info'] = $device_info;
    }
    public function setQueryCouponStockParamNonceStr(){
        if (func_num_args() == 0){
            $this->queryCouponStockParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->queryCouponStockParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setQueryCouponStockParamSign(){
        $sign = $this->createSign($this->getQueryCouponStockParameter());
        $this->queryCouponStockParameter['sign'] = $sign;
    }
    public function setQueryCouponStockParamVersion($version){
        $this->queryCouponStockParameter['version'] = $version;
    }
    public function setQueryCouponStockParamType($type){
        $this->queryCouponStockParameter['type'] = $type;
    }
    
    /*
     * 公众号：Jsapi支付和Native支付有关接口参数设置
     */
    private $unifiedorderParameter = array();
    private $queryOrderParameter = array();
    private $closeOrderParameter = array();
    private $refundParameter = array();
    private $queryRefundParameter = array();
    private $downloadBillParameter = array();
    private $reportParameter = array();
    
    /**
     * @return the $unifiedorderParameter
     */
    public function getUnifiedorderParameter()
    {
        return $this->unifiedorderParameter;
    }

 /**
     * @return the $queryOrderParameter
     */
    public function getQueryOrderParameter()
    {
        return $this->queryOrderParameter;
    }

 /**
     * @return the $closeOrderParameter
     */
    public function getCloseOrderParameter()
    {
        return $this->closeOrderParameter;
    }

 /**
     * @return the $refundParameter
     */
    public function getRefundParameter()
    {
        return $this->refundParameter;
    }

 /**
     * @return the $queryRefundParameter
     */
    public function getQueryRefundParameter()
    {
        return $this->queryRefundParameter;
    }

 /**
     * @return the $downloadBillParameter
     */
    public function getDownloadBillParameter()
    {
        return $this->downloadBillParameter;
    }

 /**
     * @return the $reportParameter
     */
    public function getReportParameter()
    {
        return $this->reportParameter;
    }

 /**
     * @param multitype: $unifiedorderParameter
     */
    public function setUnifiedorderParameter($unifiedorderParameter)
    {
        $this->unifiedorderParameter = $unifiedorderParameter;
    }

 /**
     * @param multitype: $queryOrderParameter
     */
    public function setQueryOrderParameter($queryOrderParameter)
    {
        $this->queryOrderParameter = $queryOrderParameter;
    }

 /**
     * @param multitype: $closeOrderParameter
     */
    public function setCloseOrderParameter($closeOrderParameter)
    {
        $this->closeOrderParameter = $closeOrderParameter;
    }

 /**
     * @param multitype: $refundParameter
     */
    public function setRefundParameter($refundParameter)
    {
        $this->refundParameter = $refundParameter;
    }

 /**
     * @param multitype: $queryRefundParameter
     */
    public function setQueryRefundParameter($queryRefundParameter)
    {
        $this->queryRefundParameter = $queryRefundParameter;
    }

 /**
     * @param multitype: $downloadBillParameter
     */
    public function setDownloadBillParameter($downloadBillParameter)
    {
        $this->downloadBillParameter = $downloadBillParameter;
    }

 /**
     * @param multitype: $reportParameter
     */
    public function setReportParameter($reportParameter)
    {
        $this->reportParameter = $reportParameter;
    }
    
    /*
     * 设置统一下单接口参数
     */
    public function setUnifiedOrderParamAppid($appid){
        $this->unifiedorderParameter['appid'] = $appid;
    }
    public function setUnifiedOrderParamMchId($mch_id){
        $this->unifiedorderParameter['mch_id'] = $mch_id;
    }
    public function setUnifiedOrderParamDeviceInfo($device_info){
        $this->unifiedorderParameter['device_info'] = $device_info;
    }
    public function setUnifiedOrderParamNonceStr(){
        if (func_num_args() == 0){
            $this->unifiedorderParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->unifiedorderParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setUnifiedOrderParamSign(){
        $sign = $this->createSign($this->getUnifiedorderParameter());
        $this->unifiedorderParameter['sign'] = $sign;
    }
    public function setUnifiedOrderParamBody($body){
        $this->unifiedorderParameter['body'] = $body;
    }
    public function setUnifiedOrderParamDetail($detail){
        $this->unifiedorderParameter['detail'] = $detail;
    }
    public function setUnifiedOrderParamAttach($attach){
        $this->unifiedorderParameter['attach'] = $attach;
    }
    public function setUnifiedOrderParamOutTradeNo($out_trade_no){
        $this->unifiedorderParameter['out_trade_no'] = $out_trade_no;
    }
    public function setUnifiedOrderParamFeeType($fee_type){
        $this->unifiedorderParameter['fee_type'] = $fee_type;
    }
    public function setUnifiedOrderParamTotalFee($total_fee){
        $this->unifiedorderParameter['total_fee'] = $total_fee;
    }
    public function setUnifiedOrderParamSpbillCreateIp($spbill_create_ip){
        $this->unifiedorderParameter['spbill_create_ip'] = $spbill_create_ip;
    }
    public function setUnifiedOrderParamTimeStart($time_start){
        $this->unifiedorderParameter['time_start'] = $time_start;
    }
    public function setUnifiedOrderParamTimeExpire($time_expire){
        $this->unifiedorderParameter['time_expire'] = $time_expire;
    }
    public function setUnifiedOrderParamGoodsTag($goods_tag){
        $this->unifiedorderParameter['goods_tag'] = $goods_tag;
    }
    public function setUnifiedOrderParamNotifyUrl($notify_url){
        $this->unifiedorderParameter['notify_url'] = $notify_url;
    }
    public function setUnifiedOrderParamTradeType($trade_type){
        $this->unifiedorderParameter['trade_type'] = $trade_type;
    }
    public function setUnifiedOrderParamProductId($product_id){
        $this->unifiedorderParameter['product_id'] = $product_id;
    }
    public function setUnifiedOrderParamLimitPay($limit_pay){
        $this->unifiedorderParameter['limit_pay'] = $limit_pay;
    }
    public function setUnifiedOrderParamOpenid($openid){
        $this->unifiedorderParameter['openid'] = $openid;
    }
    /*
     * 设置查询订单接口参数
     */
    public function setQueryOrderParamAppid($appid){
        $this->queryOrderParameter['appid'] = $appid;
    }
    public function setQueryOrderParamMchId($mch_id){
        $this->queryOrderParameter['mch_id'] = $mch_id;
    }
    public function setQueryOrderParamTransactionId($transaction_id){
        $this->queryOrderParameter['transaction_id'] = $transaction_id;
    }
    public function setQueryOrderParamOutTradeNo($out_trade_no){
        $this->queryOrderParameter['out_trade_no'] = $out_trade_no;
    }
    public function setQueryOrderParamNonceStr(){
        if (func_num_args() == 0){
            $this->queryOrderParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->queryOrderParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setQueryOrderParamSign(){
        $sign = $this->createSign($this->getQueryOrderParameter());
        $this->queryOrderParameter['sign'] = $sign;
    }
    /*
     * 设置关闭订单接口参数
     */
    public function setCloseOrderParamAppid($appid){
        $this->closeOrderParameter['appid'] = $appid;
    }
    public function setCloseOrderParamMchId($mch_id){
        $this->closeOrderParameter['mch_id'] = $mch_id;
    }
    public function setCloseOrderParamOutTradeNo($out_trade_no){
        $this->closeOrderParameter['out_trade_no'] = $out_trade_no;
    }
    public function setCloseOrderParamNonceStr(){
        if (func_num_args() == 0){
            $this->closeOrderParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->closeOrderParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setCloseOrderParamSign(){
        $sign = $this->createSign($this->getCloseOrderParameter());
        $this->closeOrderParameter['sign'] = $sign;
    }
    /*
     * 设置申请退款接口参数
     */
    public function setRefundParamAppid($appid){
        $this->refundParameter['appid'] = $appid;
    }
    public function setRefundParamMchId($mch_id){
        $this->refundParameter['mch_id'] = $mch_id;
    }
    public function setRefundParamDeviceInfo($device_info){
        $this->refundParameter['device_info'] = $device_info;
    }
    public function setRefundParamNonceStr(){
        if (func_num_args() == 0){
            $this->refundParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->refundParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setRefundParamSign(){
        $sign = $this->createSign($this->getRefundParameter());
        $this->refundParameter['sign'] = $sign;
    }
    public function setRefundParamTransactionId($transaction_id){
        $this->refundParameter['transaction_id'] = $transaction_id;
    }
    public function setRefundParamOutTradeNo($out_trade_no){
        $this->refundParameter['out_trade_no'] = $out_trade_no;
    }
    public function setRefundParamOutRefundNo($out_refund_no){
        $this->refundParameter['out_refund_no'] = $out_refund_no;
    }
    public function setRefundParamTotalFee($total_fee){
        $this->refundParameter['total_fee'] = $total_fee;
    }
    public function setRefundParamRefundFee($refund_fee){
        $this->refundParameter['refund_fee'] = $refund_fee;
    }
    public function setRefundParamRefundFeeType($refund_fee_type){
        $this->refundParameter['refund_fee_type'] = $refund_fee_type;
    }
    public function setRefundParamOpUserId($op_user_id){
        $this->refundParameter['op_user_id'] = $op_user_id;
    }
    /*
     * 设置查询退款接口参数
     */
    public function setQueryRefundParamAppid($appid){
        $this->queryRefundParameter['appid'] = $appid;
    }
    public function setQueryRefundParamMchId($mch_id){
        $this->queryRefundParameter['mch_id'] = $mch_id;
    }
    public function setQueryRefundParamDeviceInfo($device_info){
        $this->queryRefundParameter['device_info'] = $device_info;
    }
    public function setQueryRefundParamNonceStr(){
        if (func_num_args() == 0){
            $this->queryRefundParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->queryRefundParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setQueryRefundParamSign(){
        $sign = $this->createSign($this->getQueryRefundParameter());
        $this->queryRefundParameter['sign'] = $sign;
    }
    public function setQueryRefundParamTransactionId($transaction_id){
        $this->queryRefundParameter['transaction_id'] = $transaction_id;
    }
    public function setQueryRefundParamOutTradeNo($out_trade_no){
        $this->queryRefundParameter['out_trade_no'] = $out_trade_no;
    }
    public function setQueryRefundParamOutRefundNo($out_refund_no){
        $this->queryRefundParameter['out_refund_no'] = $out_refund_no;
    }
    public function setQueryRefundParamRefundId($refund_id){
        $this->queryRefundParameter['refund_id'] = $refund_id;
    }
    /*
     * 设置下载对账单接口参数
     */
    public function setDownloadBillParamAppid($appid){
        $this->downloadBillParameter['appid'] = $appid;
    }
    public function setDownloadBillParamMchId($mch_id){
        $this->downloadBillParameter['mch_id'] = $mch_id;
    }
    public function setDownloadBillParamDeviceInfo($device_info){
        $this->downloadBillParameter['device_info'] = $device_info;
    }
    public function setDownloadBillParamNonceStr(){
        if (func_num_args() == 0){
            $this->downloadBillParameter['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->downloadBillParameter['nonce_str'] = $nonce_str;
        }
    }
    public function setDownloadBillParamSign(){
        $sign = $this->createSign($this->getDownloadBillParameter());
        $this->downloadBillParameter['sign'] = $sign;
    }
    public function setDownloadBillParamBillDate($bill_date){
        $this->downloadBillParameter['bill_date'] = $bill_date;
    }
    public function setDownloadBillParamBillType($bill_type){
        $this->downloadBillParameter['bill_type'] = $bill_type;
    }
    
    /*
     * 扫码支付生成二维码参数属性
     */
    private $nativeModelOne = array();
    private $nativeModelTwo = array();
    /*
     * 生成网页内调起支付参数属性
     */
    private $jsapiParameter = array();
    /**
     * @return the $jsapiParameter
     */
    public function getJsapiParameter()
    {
        return $this->jsapiParameter;
    }

 /**
     * @param multitype: $jsapiParameter
     */
    public function setJsapiParameter($jsapiParameter)
    {
        $this->jsapiParameter = $jsapiParameter;
    }

 /**
     * @return the $nativeModelOne
     */
    public function getNativeModelOne()
    {
        return $this->nativeModelOne;
    }

 /**
     * @return the $nativeModelTwo
     */
    public function getNativeModelTwo()
    {
        return $this->nativeModelTwo;
    }

 /**
     * @param multitype: $nativeModelOne
     */
    public function setNativeModelOne($nativeModelOne)
    {
        $this->nativeModelOne = $nativeModelOne;
    }

 /**
     * @param multitype: $nativeModelTwo
     */
    public function setNativeModelTwo($nativeModelTwo)
    {
        $this->nativeModelTwo = $nativeModelTwo;
    }
    /*
     * 设置native支付模式一生成二维码参数
     */
    public function setNativeModelOneParamAppid($appid){
        $this->nativeModelOne['appid'] = $appid;
    }
    public function setNativeModelOneParamMchId($mch_id){
        $this->nativeModelOne['mch_id'] = $mch_id;
    }
    public function setNativeModelOneParamTimeStamp($time_stamp){
        $this->nativeModelOne['time_stamp'] = $time_stamp;
    }
    public function setNativeModelOneParamNonceStr(){
        if (func_num_args() == 0){
            $this->nativeModelOne['nonce_str'] = $this->createNonceStr();
        }else {
            $args = func_get_args();
            $nonce_str = $args[0];
            $this->nativeModelOne['nonce_str'] = $nonce_str;
        }
    }
    public function setNativeModelOneParamProductId($product_id){
        $this->nativeModelOne['product_id'] = $product_id;
    }
    public function setNativeModelOneParamSign(){
        $this->nativeModelOne['sign'] = $this->createSign($this->getNativeModelOne());
    }

    /*
     * 设置jsapi网页内支付所需参数
     */
    public function setJsapiParamAppId($appId){
        $this->jsapiParameter['appId'] = $appId;
    }
    public function setJsapiParamTimeStamp($timeStamp){
        $this->jsapiParameter['timeStamp'] = $timeStamp;
    }
    public function setJsapiParamNonceStr(){
        $this->jsapiParameter['nonceStr'] = $this->createNonceStr();
    }
    public function setJsapiParamPackage($package){
        $this->jsapiParameter['package'] = $package;
    }
    public function setJsapiParamSignType($signType){
        $this->jsapiParameter['signType'] = $signType;
    }
    public function setJsapiParamPaySign(){
        $this->jsapiParameter['paySign'] = $this->createSign($this->getJsapiParameter());
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