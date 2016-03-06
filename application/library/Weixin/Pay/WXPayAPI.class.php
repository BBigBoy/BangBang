<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付接口api
 */
class WXPayAPI{
    
    private $common;
    private $manage;
    
    function __construct(){
        $this->common = new WXPayCommon();
        $this->manage = new WXPayManage();
    }
    /*
     * 描述：企业付款api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     * @exception 数据库存储异常
     */
    public function transfers($xml){
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            $return_array = $this->common->xmlToArray($result);
            if ($return_array['result_code'] == "SUCCESS"){
                //记录企业付款明细
                $param_array = $this->common->xmlToArray($xml);
                $array = array(
                    'mch_appid' => $param_array['mch_appid'],
                    'mchid' => $param_array['mchid'],
                    'nonce_str' => $param_array['nonce_str'],
                    'partner_trade_no' => $return_array['partner_trade_no'],
                    'openid' => $param_array['openid'],
                    'check_name' => $param_array['check_name'],
                    'amount' => $param_array['amount'],
                    'desc' => $param_array['desc'],
                    'spbill_create_ip' => $param_array['spbill_create_ip'],
                    'payment_time' => $return_array['payment_time'],
                    'payment_no' => $return_array['payment_no']
                );
                $this->manage->transfersLog($array);
            }
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：查询企业付款api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function queryTransfers($xml) {
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：现金红包api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     * @exception 数据库存储异常
     */
    public function sendRedpack($xml){
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            $return_array = $this->common->xmlToArray($result);
            if ($return_array['result_code'] == "SUCCESS"){
                //记录红包发放明细
                $param_array = $this->common->xmlToArray($xml);
                $array = array(
                    'nonce_str' => $param_array['nonce_str'],
                    'mch_id' => $param_array['mch_id'],
                    'mch_billno' => $return_array['mch_billno'],
                    'wxappid' => $return_array['wxappid'],
                    'send_name' => $param_array['send_name'],
                    're_openid' => $param_array['re_openid'],
                    'total_amount' => $param_array['total_amount'],
                    'total_num' => $param_array['total_num'],
                    'wishing' => $param_array['wishing'],
                    'client_ip' => $param_array['client_ip'],
                    'act_name' => $param_array['act_name'],
                    'remark' => $param_array['remark'],
                    'send_time' => $return_array['send_time'],
                    'send_listid' => $return_array['send_listid']
                );
                $this->manage->redpackLog($array);
            }
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：查询现金红包接口api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function queryRedpack($xml) {
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：裂变红包api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     * @exception 数据库存储异常
     */
    public function sendGroupRedpack($xml) {
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendgroupredpack";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            $return_array = $this->common->xmlToArray($result);
            if ($return_array['result_code'] == "SUCCESS"){
                //记录裂变红包发放明细
                $param_array = $this->common->xmlToArray($xml);
                $array = array(
                    'nonce_str' => $param_array['nonce_str'],
                    'mch_billno' => $param_array['mch_billno'],
                    'mch_id' => $param_array['mch_id'],
                    'wxappid' => $param_array['wxappid'],
                    'send_name' => $param_array['send_name'],
                    're_openid' => $return_array['re_openid'],
                    'total_amount' => $param_array['total_amount'],
                    'total_num' => $param_array['total_num'],
                    'amt_type' => $param_array['amt_type'],
                    'wishing' => $param_array['wishing'],
                    'act_name' => $param_array['act_name'],
                    'remark' => $param_array['remark'],
                    'send_time' => $return_array['send_time'],
                    'send_listid' => $return_array['send_listid']
                );
                $this->manage->groupRedpackLog($array);
            }
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：代金卷api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     * @exception 数据库存储异常
     */
    public function sendCoupon($xml){
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/send_coupon";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            $return_array = $this->common->xmlToArray($result);
            if ($return_array['result_code'] == "SUCCESS"){
                //记录代金券发放明细
                $param_array = $this->common->xmlToArray($xml);
                $array = array(
                    'coupon_stock_id' => $param_array['coupon_stock_id'],
                    'openid_count' => $param_array['openid_count'],
                    'partner_trade_no' => $param_array['partner_trade_no'],
                    'openid' => $param_array['openid'],
                    'appid' => $param_array['appid'],
                    'mch_id' => $param_array['mch_id'],
                    'nonce_str' => $param_array['nonce_str'],
                    'send_time' => "".date("Y-m-d H:i:s")
                );
                $this->manage->couponLog($array);
            }
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：查询代金卷api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function queryCoupon($xml){
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/mmpaymkttransfers/query_coupon_stock";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：生成Native支付模式一url
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function createNativeModelOneUrl($parameters){
        if ($parameters){
            $url = "weixin://wxpay/bizpayurl?".
                "sign=".$parameters['sign']."&".
                "appid=".$parameters['appid']."&".
                "mch_id=".$parameters['mch_id']."&".
                "product_id=".$parameters['product_id']."&".
                "time_stamp=".$parameters['time_stamp']."&".
                "nonce_str=".$parameters['nonce_str'];
            return $url;
        }else {
            return false;
        }
    }
    /*
     * 描述：统一下单api
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function unifiedorder($xml){
        if ($xml){
            $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else {
            return false;
        }
    }
    /*
     * 描述：查询订单
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function orderQuery($xml){
        if($xml){
            $url = "https://api.mch.weixin.qq.com/pay/orderquery";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else{
            return false;
        }
    }
    /*
     * 描述：关闭订单
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function closeOrder($xml){
        if($xml){
            $url = "https://api.mch.weixin.qq.com/pay/closeorder";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else{
            return false;
        }
    }
    /*
     * 描述：申请退款
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function refund($xml) {
        if($xml){
            $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
            $result = $this->common->postxmlSSLCurl($xml, $url);
            return $result;
        }else{
            return false;
        }
    }
    /*
     * 描述：查询退款
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function refundQuery($xml){
        if($xml){
            $url = "https://api.mch.weixin.qq.com/pay/refundquery";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else{
            return false;
        }
    }
    /*
     * 描述：下载对账单
     * @param xml 接口需要的包装成xml类型的数据
     * @return 参数错误则返回false，否则返回接口通过调用后返回的数据
     */
    public function downLoadBill($xml){
        if($xml){
            $url = "https://api.mch.weixin.qq.com/pay/downloadbill";
            $result = $this->common->postXmlCurl($xml, $url);
            return $result;
        }else{
            return false;
        }
    }

}

?>