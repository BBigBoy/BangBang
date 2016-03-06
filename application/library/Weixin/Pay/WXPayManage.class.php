<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付管理类
 * 以支付日志的形式保存在数据库中
 */
class WXPayManage{
    /*
     * 保存企业付款明细
     */
    public function transfersLog($array){
        $TransfersLog = M('transfers_log');
        if ($TransfersLog->create($array)){
            if ($TransfersLog->add()){
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /*
     * 保存现金红包明细
     */
    public function redpackLog($array){
        $RedpackLog = M('redpack_log');
        if ($RedpackLog->create($array)){
            if ($RedpackLog->add()){
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /*
     * 保存裂变红包明细
     */
    public function groupRedpackLog($array){
        $GroupRedpackLog = M('groupredpack_log');
        if ($GroupRedpackLog->create($array)){
            if ($GroupRedpackLog->add()){
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
    /*
     * 保存代金券发放明细
     */
    public function couponLog($array){
        $CouponLog = M('coupon_log');
        if ($CouponLog->create($array)){
            if ($CouponLog->add()){
                return true;
            }else {
                return false;
            }
        }else {
            return false;
        }
    }
}

?>