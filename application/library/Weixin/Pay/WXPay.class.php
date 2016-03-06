<?php
namespace Platform\Common\WXPay;

class WXPay{
    
    private $common;
    private $parameter;
    private $checkparam;
    private $api;
    
    public function __construct(){
        $this->common = new WXPayCommon();
        $this->parameter = new WXPayAPIData();
        $this->checkparam = new WXPayParamValidate();
        $this->api = new WXPayAPI();
    }
    /*
     * 描述：企业付款接口
     * @param device_info 设备号（可以为null）
     * @param partner_trade_no 商户订单号
     * @param openid 用户在对应公众号下的openid
     * @param check_name 校验用户真实姓名
                    （NO_CHECK：不校验真实姓名 
        FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） 
        OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功））
     * @param re_user_name 收款用户姓名
     * @param amount 付款金额
     * @param desc 企业付款描述信息
     * @return 微信企业付款api返回的支付结果信息
     */
    public function transfers($device_info,$partner_trade_no,$openid,$check_name,$re_user_name,$amount,$desc){
        //构建参数
        $this->parameter->setTransfersParamMchAppid(WXPayConf::APPID);
        $this->parameter->setTransfersParamMchid(WXPayConf::MCHID);
        $this->parameter->setTransfersParamDeviceInfo($device_info);
        $this->parameter->setTransfersParamNonceStr();
        $this->parameter->setTransfersParamPartnerTradeNo($partner_trade_no);
        $this->parameter->setTransfersParamOpenid($openid);
        $this->parameter->setTransfersParamCheckName($check_name);
        $this->parameter->setTransfersParamReUserName($re_user_name);
        $this->parameter->setTransfersParamAmount($amount);
        $this->parameter->setTransfersParamDesc($desc);
        $this->parameter->setTransfersParamSpbillCreateIp($_SERVER['SERVER_ADDR']);
        $this->parameter->setTransfersParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getTransfersParameter();
        $validate_result = $this->checkparam->validateTransfersParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->transfers($validate_result);
        return $commit_result;
    }
    /*
     * 描述：查询企业付款信息
     * @param partner_trade_no 商户订单号 
     * @return 微信查询企业付款api返回的企业付款信息
     */
    public function getTransfersInfo($partner_trade_no){
        //构建参数
        $this->parameter->setQueryTransfersParamNonceStr();
        $this->parameter->setQueryTransfersParamPartnerTradeNo($partner_trade_no);
        $this->parameter->setQueryTransfersParamMchId(WXPayConf::MCHID);
        $this->parameter->setQueryTransfersParamAppid(WXPayConf::APPID);
        $this->parameter->setQueryTransfersParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getQueryTransfersInfoParameter();
        $validate_result = $this->checkparam->validateQueryTransfersParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->queryTransfers($validate_result);
        return $commit_result;
    }
    /*
     * 描述：现金红包(固定金额红包)
     * @param mch_billno 商户订单号
     * @param send_name 商户名称
     * @param re_openid 用户openid
     * @param total_amount 红包金额
     * @param wishing 红包祝福语
     * @param act_name 活动名称
     * @param remark 备注
     * @return 微信现金红包api返回的红包发放结果信息
     */
    public function sendRedpack($mch_billno,$send_name,$re_openid,$total_amount,$wishing,$act_name,$remark){
        //构建参数
        $this->parameter->setSendRedpackParamNonceStr();
        $this->parameter->setSendRedpackParamMchBillno($mch_billno);
        $this->parameter->setSendRedpackParamMchId(WXPayConf::MCHID);
        $this->parameter->setSendRedpackParamWxappid(WXPayConf::APPID);
        $this->parameter->setSendRedpackParamSendName($send_name);
        $this->parameter->setSendRedpackParamReOpenid($re_openid);
        $this->parameter->setSendRedpackParamTotalAmount($total_amount);
        $this->parameter->setSendRedpackParamTotalNum(1);
        $this->parameter->setSendRedpackParamWishing($wishing);
        $this->parameter->setSendRedpackParamClientIp($_SERVER['SERVER_ADDR']);
        $this->parameter->setSendRedpackParamActName($act_name);
        $this->parameter->setSendRedpackParamRemark($remark);
        $this->parameter->setSendRedpackParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getSendRedpackParameter();
        $validate_result = $this->checkparam->validateRedpackParameters($parameters);
        //把经过检查的参数提交到对应接口中
        $commit_result = $this->api->sendRedpack($validate_result);
        return $commit_result;
    }
    /*
     * 描述：现金红包（随机金额红包)
     * @param mch_billno 商户订单号
     * @param send_name 商户名称
     * @param re_openid 用户openid
     * @param min_amount 最小红包金额数
     * @param max_amount 最大红包金额数
     * @param wishing 红包祝福语
     * @param act_name 活动名称
     * @param remark 备注
     * @return 微信现金红包api返回的红包发放结果信息
     */
    public function sendRandomRedpack($mch_billno,$send_name,$re_openid,$min_amount,$max_amount,$wishing,$act_name,$remark){
        if($min_amount > $max_amount){
            $errorMsg = date("Y-m-d H:i:s")." 随机红包最小金额数大于最大金额数";
            return $errorMsg;
        }
        $total_amount = rand($min_amount, $max_amount);
        //构建参数
        $this->parameter->setSendRedpackParamNonceStr();
        $this->parameter->setSendRedpackParamMchBillno($mch_billno);
        $this->parameter->setSendRedpackParamMchId(WXPayConf::MCHID);
        $this->parameter->setSendRedpackParamWxappid(WXPayConf::APPID);
        $this->parameter->setSendRedpackParamSendName($send_name);
        $this->parameter->setSendRedpackParamReOpenid($re_openid);
        $this->parameter->setSendRedpackParamTotalAmount($total_amount);
        $this->parameter->setSendRedpackParamTotalNum(1);
        $this->parameter->setSendRedpackParamWishing($wishing);
        $this->parameter->setSendRedpackParamClientIp($_SERVER['SERVER_ADDR']);
        $this->parameter->setSendRedpackParamActName($act_name);
        $this->parameter->setSendRedpackParamRemark($remark);
        $this->parameter->setSendRedpackParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getSendRedpackParameter();
        $validate_result = $this->checkparam->validateRedpackParameters($parameters);
        //把经过检查的参数提交到对应接口中
        $commit_result = $this->api->sendRedpack($validate_result);
        return $commit_result;
    }
    /*
     * 描述：红包查询
     * @param mch_billno 商户订单号
     * @return 返回商户订单号指定的红包的发放信息记录
     */
    public function getHbInfo($mch_billno){
        //构建参数
        $this->parameter->setQueryRedpackInfoParamNonceStr();
        $this->parameter->setQueryRedpackInfoParamMchBillno($mch_billno);
        $this->parameter->setQueryRedpackInfoParamMchId(WXPayConf::MCHID);
        $this->parameter->setQueryRedpackInfoParamAppid(WXPayConf::APPID);
        $this->parameter->setQueryRedpackInfoParamBillType("MCHT");
        $this->parameter->setQueryRedpackInfoParamSign();
        //获取构建参数，并对参数做检查
        $parameters = $this->parameter->getQueryRedpackInfoParameter();
        $validate_result = $this->checkparam->validateQueryRedpackParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->queryRedpack($validate_result);
        return $commit_result;
    }
    /*
     * 描述：裂变红包
     * @param mch_billno 商户订单号
     * @param send_name 商户名称
     * @param re_openid 用户的openid
     * @param total_amount 红包总金额
     * @param total_num 红包发放总人数
     * @param wishing 红包祝福语
     * @param act_name 活动名称
     * @param remark 备注
     */
    public function sendGroupRedpack($mch_billno,$send_name,$re_openid,$total_amount,$total_num,$wishing,$act_name,$remark){
        //构建参数
        $this->parameter->setSendGroupRedpackParamNonceStr();
        $this->parameter->setSendGroupRedpackParamMchBillno($mch_billno);
        $this->parameter->setSendGroupRedpackParamMchId(WXPayConf::MCHID);
        $this->parameter->setSendGroupRedpackParamWxappid(WXPayConf::APPID);
        $this->parameter->setSendGroupRedpackParamSendName($send_name);
        $this->parameter->setSendGroupRedpackParamReOpenid($re_openid);
        $this->parameter->setSendGroupRedpackParamTotalAmount($total_amount);
        $this->parameter->setSendGroupRedpackParamTotalNum($total_num);
        $this->parameter->setSendGroupRedpackParamAmtType("ALL_RAND");
        $this->parameter->setSendGroupRedpackParamWishing($wishing);
        $this->parameter->setSendGroupRedpackParamActName($act_name);
        $this->parameter->setSendGroupRedpackParamRemark($remark);
        $this->parameter->setSendGroupRedpackParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getSendGroupRedpackParameter();
        $validate_result = $this->checkparam->validateGroupRedpackParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->sendGroupRedpack($validate_result);
        return $commit_result;
    }
    /*
     * 描述：代金券或立减优惠
     * @param coupon_stock_id 代金券批次id
     * @param partner_trade_no 商户单据号
     * @param openid 用户openid
     * @param op_user_id 操作员(可为null)
     * @param device_info 设备号(可为null)
     * @return 微信代金券或立减优惠发放结果信息
     */
    public function sendCoupon($coupon_stock_id,$partner_trade_no,$openid,$op_user_id,$device_info){
        //构建参数
        $this->parameter->setSendCouponParamCouponStockId($coupon_stock_id);
        $this->parameter->setSendCouponParamOpenidCount(1);
        $this->parameter->setSendCouponParamPartnerTradeNo($partner_trade_no);
        $this->parameter->setSendCouponParamOpenid($openid);
        $this->parameter->setSendCouponParamAppid(WXPayConf::APPID);
        $this->parameter->setSendCouponParamMchId(WXPayConf::MCHID);
        $this->parameter->setSendCouponParamOpUserId($op_user_id);
        $this->parameter->setSendCouponParamDeviceInfo($device_info);
        $this->parameter->setSendCouponParamNonceStr();
        $this->parameter->setSendCouponParamVersion("1.0");
        $this->parameter->setSendCouponParamType("XML");
        $this->parameter->setSendCouponParamSign();
        //获取所构建参数,并对参数做检查
        $parameters = $this->parameter->getSendCouponParameter();
        $validate_result = $this->checkparam->validateCouponParameters($parameters);
        //把经过检查的参数提交到相对应的接口
        $commit_result = $this->api->sendCoupon($validate_result);
        return $commit_result;
    }
    /*
     * 描述：查询代金券批次
     * @param coupon_stock_id 代金券批次
     * @param op_user_id 操作员（可为null）
     * @param device_info 设备号（可为null）
     * @return 返回微信代金券批次id所对应的代金券或立减优惠发放结果信息
     */
    public function queryCouponStock($coupon_stock_id,$op_user_id,$device_info){
        //构建参数
        $this->parameter->setQueryCouponStockParamCouponStockId($coupon_stock_id);
        $this->parameter->setQueryCouponStockParamAppid(WXPayConf::APPID);
        $this->parameter->setQueryCouponStockParamMchId(WXPayConf::MCHID);
        $this->parameter->setQueryCouponStockParamOpUserId($op_user_id);
        $this->parameter->setQueryCouponStockParamDeviceInfo($device_info);
        $this->parameter->setQueryCouponStockParamNonceStr();
        $this->parameter->setQueryCouponStockParamVersion("1.0");
        $this->parameter->setQueryCouponStockParamType("XML");
        $this->parameter->setQueryCouponStockParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getQueryCouponStockParameter();
        $validate_result = $this->checkparam->validateQueryCouponParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->queryCoupon($validate_result);
        return $commit_result;
    }
    /*
     * 描述：扫码支付（模式一）
     * @param product_id 商品编号
     * @return 用来生成二维码的链接（模式一）
     */
    public function nativeModelOne($product_id){
        //构建参数
        $this->parameter->setNativeModelOneParamAppid(WXPayConf::APPID);
        $this->parameter->setNativeModelOneParamMchId(WXPayConf::MCHID);
        $this->parameter->setNativeModelOneParamProductId($product_id);
        $this->parameter->setNativeModelOneParamTimeStamp("".time());
        $this->parameter->setNativeModelOneParamNonceStr();
        $this->parameter->setNativeModelOneParamSign();
        //获取所构建参数，并对参数做检查
        $parameters = $this->parameter->getNativeModelOne();
        $validate_result = $this->checkparam->validateNativeModelOneParameters($parameters);
        //把参数提交到相应接口
        $commit_result = $this->api->createNativeModelOneUrl($validate_result);
        return $commit_result;
    }
    /*
     * 描述：公众平台后天回调url
     * @param device_info 设备号（可为null）
     * @param body 商品描述
     * @param detail 商品详情（可为null）
     * @param attach 附加数据（可为null）
     * @param out_trade_no 商户订单号
     * @param fee_type 货币类型（可为null）
     * @param total_fee 商品总金额
     * @param time_start 交易起始时间（可为null）
     * @param time_expire 交易结束时间（可为null）
     * @param goods_tag 商品标记（可为null）
     * @param product_id 商品id
     * @param limit_pay 指定支付方式（可为null）
     * @param openid 用户的openid
     * @return 返回微信支付系统统一下单返回的下单结果
     */
    public function publicPlatformBackgroundCallbackURL($device_info,$body,$detail,$attach,$out_trade_no,
        $fee_type,$total_fee,$time_start,$time_expire,$goods_tag,$limit_pay){
        $wxpay_server_return_xml = file_get_contents('php://input','r');
        $wxpay_server_return_arr = $this->common->xmlToArray($wxpay_server_return_xml);
        $unified_order_return_data = $this->unifiedOrder($device_info, $body, $detail, $attach, $out_trade_no, $fee_type, 
            $total_fee, $time_start, $time_expire, $goods_tag, WXPayConf::SCANCODE_NOTIFY_URL, "NATIVE", 
            $wxpay_server_return_arr['product_id'], $limit_pay, $wxpay_server_return_arr['openid']);
        return $unified_order_return_data;
    }
    /*
     * 描述：统一下单
     * @param device_info 设备号（可为null）
     * @param body 商品描述
     * @param detail 商品详情（可为null）
     * @param attach 附加数据（可为null）
     * @param out_trade_no 商户订单号
     * @param fee_type 货币类型（可为null）
     * @param total_fee 商品总金额
     * @param time_start 交易起始时间（可为null）
     * @param time_expire 交易结束时间（可为null）
     * @param goods_tag 商品标记（可为null）
     * @param notify_url 通知地址
     * @param trade_type 交易类型（其值为：JSAPI，NATIVE，APP）
     * @param product_id 商品id（可为null）
     * @param limit_pay 指定支付方式（可为null）
     * @param openid 用户的openid（可为null）
     * @return 返回微信支付系统统一下单返回的下单结果
     */
    public function unifiedOrder($device_info,$body,$detail,$attach,$out_trade_no,
        $fee_type,$total_fee,$time_start,$time_expire,$goods_tag,$notify_url,
        $trade_type,$product_id,$limit_pay,$openid){
        //构建参数
        $this->parameter->setUnifiedOrderParamAppid(WXPayConf::APPID);
        $this->parameter->setUnifiedOrderParamMchId(WXPayConf::MCHID);
        $this->parameter->setUnifiedOrderParamDeviceInfo($device_info);
        $this->parameter->setUnifiedOrderParamNonceStr();
        $this->parameter->setUnifiedOrderParamBody($body);
        $this->parameter->setUnifiedOrderParamDetail($detail);
        $this->parameter->setUnifiedOrderParamAttach($attach);
        $this->parameter->setUnifiedOrderParamOutTradeNo($out_trade_no);
        $this->parameter->setUnifiedOrderParamFeeType($fee_type);
        $this->parameter->setUnifiedOrderParamTotalFee($total_fee);
        $this->parameter->setUnifiedOrderParamSpbillCreateIp($_SERVER['SERVER_ADDR']);
        $this->parameter->setUnifiedOrderParamTimeStart($time_start);
        $this->parameter->setUnifiedOrderParamTimeExpire($time_expire);
        $this->parameter->setUnifiedOrderParamGoodsTag($goods_tag);
        $this->parameter->setUnifiedOrderParamNotifyUrl($notify_url);
        $this->parameter->setUnifiedOrderParamTradeType($trade_type);
        $this->parameter->setUnifiedOrderParamProductId($product_id);
        $this->parameter->setUnifiedOrderParamLimitPay($limit_pay);
        $this->parameter->setUnifiedOrderParamOpenid($openid);
        $this->parameter->setUnifiedOrderParamSign();
        //获取所构建的参数，并对参数做检查
        $parameters = $this->parameter->getUnifiedorderParameter();
        $validate_result = $this->checkparam->validateUnifiedOrderParameters($parameters);
        //把参数提交到统一下单接口
        $commit_result = $this->api->unifiedorder($validate_result);
        return $commit_result;
    }
    /*
     * 描述：扫码支付（模式二）
     * @param device_info 设备号（可为null）
     * @param body 商品描述
     * @param detail 商品详情（可为null）
     * @param attach 附加数据（可为null）
     * @param out_trade_no 商户订单号
     * @param fee_type 货币类型（可为null）
     * @param total_fee 商品总金额(以分为单位)
     * @param time_start 交易起始时间（可为null）
     * @param time_expire 交易结束时间（可为null）
     * @param goods_tag 商品标记（可为null）
     * @param notify_url 通知地址
     * @param product_id 商品id
     * @param limit_pay 指定支付方式（可为null）
     * @param openid 用户的openid（可为null）
     * @return 返回需要生成二维码的链接
     */
    public function nativeModelTwo($device_info,$body,$detail,$attach,$out_trade_no,
        $fee_type,$total_fee,$time_start,$time_expire,$goods_tag,$product_id,$limit_pay,$openid){
        //统一下单获取统一下单返回的code_url
        $unifiedorder_return_xml = $this->unifiedOrder($device_info, $body, $detail, $attach, 
            $out_trade_no, $fee_type, $total_fee, $time_start, $time_expire, $goods_tag, 
            WXPayConf::SCANCODE_NOTIFY_URL, "NATIVE", $product_id, $limit_pay, $openid);
        $unifiedorder_return_array = $this->common->xmlToArray($unifiedorder_return_xml);
        return $unifiedorder_return_array['code_url'];
    }
    /*
     * 描述：网页内支付
     * @param device_info 设备号（可为null）
     * @param body 商品描述
     * @param detail 商品详情（可为null）
     * @param attach 附加数据（可为null）
     * @param out_trade_no 商户订单号
     * @param fee_type 货币类型（可为null）
     * @param total_fee 商品总金额
     * @param time_start 交易起始时间（可为null）
     * @param time_expire 交易结束时间（可为null）
     * @param goods_tag 商品标记（可为null）
     * @param notify_url 通知地址
     * @param product_id 商品id（可为null）
     * @param limit_pay 指定支付方式（可为null）
     * @param openid 用户的openid（可为null）
     * @return 返回网页内调起微信支付所需的json类型参数
     */
    public function JSAPI($device_info,$body,$detail,$attach,$out_trade_no,
        $fee_type,$total_fee,$time_start,$time_expire,$goods_tag,
        $product_id,$limit_pay,$openid){
        //获取统一下单返回的结果
        $unifiedorder_return_xml = $this->unifiedOrder($device_info, $body, $detail, $attach, $out_trade_no, 
            $fee_type, $total_fee, $time_start, $time_expire, $goods_tag, WXPayConf::APPID, 
            "JSAPI", $product_id, $limit_pay, $openid);
        //从统一下单返回的结果中获取prepay_id参数
        $unifiedorder_return_array = $this->common->xmlToArray($unifiedorder_return_xml);
        $prepay_id = $unifiedorder_return_array['prepay_id'];
        //构建jsapi参数
        $this->parameter->setJsapiParamAppId(WXPayConf::APPID);
        $this->parameter->setJsapiParamNonceStr();
        $this->parameter->setJsapiParamPackage("prepay_id=".$prepay_id);
        $this->parameter->setJsapiParamSignType("MD5");
        $this->parameter->setJsapiParamTimeStamp("".time());
        $this->parameter->setJsapiParamPaySign();
        //获取所构建参数，并对参数做检查
        $jsapiParams = $this->parameter->getJsapiParameter();
        $jsapiParameter_json = $this->checkparam->validateJsapiParameters($jsapiParams);
        //返回网页内支付所需参数的json格式
        return $jsapiParameter_json;
    }
    /*
     * 描述：查询订单
     * @param transaction_id 微信订单号（可为null）
     * @param out_trade_no 商户订单号（可为null）
     * （注：微信订单号和商户订单号至少需要一个不为null）
     * @return 订单的结果
     */
    public function orderQuery($transaction_id,$out_trade_no){
        //构建参数
        $this->parameter->setQueryOrderParamAppid(WXPayConf::APPID);
        $this->parameter->setQueryOrderParamMchId(WXPayConf::MCHID);
        $this->parameter->setQueryOrderParamTransactionId($transaction_id);
        $this->parameter->setQueryOrderParamOutTradeNo($out_trade_no);
        $this->parameter->setQueryOrderParamNonceStr();
        $this->parameter->setQueryOrderParamSign();
        //获取所构建参数并对参数做检查
        $parameters = $this->parameter->getQueryOrderParameter();
        $validate_result = $this->checkparam->validateQueryOrderParameters($parameters);
        //把经过查询的参数提交到对应接口
        $commit_result = $this->api->orderQuery($validate_result);
        return $commit_result;
    }
    /*
     * 描述：关闭订单
     * @param out_trade_no 商户订单号
     * @return 订单关闭结果
     */
    public function closeOrder($out_trade_no){
        //构建参数
        $this->parameter->setCloseOrderParamAppid(WXPayConf::APPID);
        $this->parameter->setCloseOrderParamMchId(WXPayConf::MCHID);
        $this->parameter->setCloseOrderParamOutTradeNo($out_trade_no);
        $this->parameter->setCloseOrderParamNonceStr();
        $this->parameter->setCloseOrderParamSign();
        //获取所构建参数并对参数做检查
        $parameters = $this->parameter->getCloseOrderParameter();
        $validate_result = $this->checkparam->validateCloseOrderParameters($parameters);
        //把经过检查的参数提交到相对应接口
        $commit_result = $this->api->closeOrder($validate_result);
        return $commit_result;
    }
    /*
     * 描述：申请退款
     * @param device_info 设备号（可为null）
     * @param transaction_id 微信订单号（可为null）
     * @param out_trade_no 商户订单号（可为null）
     * （注：微信订单号和商户订单号不能同时为null）
     * @param out_refund_no 商户退款单号
     * @param total_fee 总金额
     * @param refund_fee 退款金额
     * @param refund_fee_type 货币类型（可为null）
     * @param op_user_id 操作员（默认为商户号）
     * @return 返回退款结果
     */
    public function refund($device_info,$transaction_id,$out_trade_no,
        $out_trade_no,$out_refund_no,$total_fee,$refund_fee,
        $refund_fee_type,$op_user_id){
        //构建参数
        $this->parameter->setRefundParamAppid(WXPayConf::APPID);
        $this->parameter->setRefundParamMchId(WXPayConf::MCHID);
        $this->parameter->setRefundParamDeviceInfo($device_info);
        $this->parameter->setRefundParamNonceStr();
        $this->parameter->setRefundParamTransactionId($transaction_id);
        $this->parameter->setRefundParamOutTradeNo($out_trade_no);
        $this->parameter->setRefundParamOutRefundNo($out_refund_no);
        $this->parameter->setRefundParamTotalFee($total_fee);
        $this->parameter->setRefundParamRefundFee($refund_fee);
        $this->parameter->setRefundParamRefundFeeType($refund_fee_type);
        $this->parameter->setRefundParamOpUserId($op_user_id);
        $this->parameter->setRefundParamSign();
        //获取所构建的参数并对参数做检查
        $parameters = $this->parameter->getRefundParameter();
        $validate_result = $this->checkparam->validateRefundParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->refund($validate_result);
        return $commit_result;
    }
    /*
     * 描述：查询退款
     * @param device_info 设备号（可为null）
     * @param transaction_id 微信订单号（可为null）
     * @param out_trade_no 商户订单号（可为null）
     * @param out_refund_no 商户退款单号（可为null）
     * @param refund_id 微信退款单号（可为null）
     * （注：微信订单号、商户订单号、商户退款单号和微信退款单号至少有一个不能null）
     * @return 返回退款结果
     */
    public function refundQuery($device_info,$transaction_id,$out_trade_no,
        $out_refund_no,$refund_id){
        //构建参数
        $this->parameter->setQueryRefundParamAppid(WXPayConf::APPID);
        $this->parameter->setQueryRefundParamMchId(WXPayConf::MCHID);
        $this->parameter->setQueryRefundParamDeviceInfo($device_info);
        $this->parameter->setQueryRefundParamNonceStr();
        $this->parameter->setQueryRefundParamTransactionId($transaction_id);
        $this->parameter->setQueryRefundParamOutTradeNo($out_trade_no);
        $this->parameter->setQueryRefundParamOutRefundNo($out_refund_no);
        $this->parameter->setQueryRefundParamRefundId($refund_id);
        $this->parameter->setQueryRefundParamSign();
        //获取所构建参数并对其做检查
        $parameters = $this->parameter->getQueryRefundParameter();
        $validate_result = $this->checkparam->validateQueryRefundParameters($parameters);
        //把经过检查的参数提交到对应接口
        $commit_result = $this->api->refundQuery($validate_result);
        return $commit_result;
    }
    /*
     * 描述：下载对账单
     * @param device_info 设备号（可为null）
     * @param bill_date 对账单日期
     * @param bill_type 账单类型（可为null）
     * @return 返回下载的账单结果
     */
    public function downLoadBill($device_info,$bill_date,$bill_type){
        //构建参数
        $this->parameter->setDownloadBillParamAppid(WXPayConf::APPID);
        $this->parameter->setDownloadBillParamMchId(WXPayConf::MCHID);
        $this->parameter->setDownloadBillParamDeviceInfo($device_info);
        $this->parameter->setDownloadBillParamNonceStr();
        $this->parameter->setDownloadBillParamBillDate($bill_date);
        $this->parameter->setDownloadBillParamBillType($bill_type);
        $this->parameter->setDownloadBillParamSign();
        //获取所构建参数并对参数做检查
        $parameters = $this->parameter->getDownloadBillParameter();
        $validate_result = $this->checkparam->validateDownloadBillParameters($parameters);
        //把经过检查的参数提交到相对应的接口
        $commit_result = $this->api->downLoadBill($validate_result);
        return $commit_result;
    }
    
}

?>