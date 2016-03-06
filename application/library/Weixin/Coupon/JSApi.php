<?php
class Weixin_Coupon_JSApi
{
    const GetCouponApiTicketUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=';

    function getCardExt($cardId)
    {
        $timestamp = time();
        $cardExt['timestamp'] = (string)$timestamp;
        $cardExt['nonce_str'] = $this->createNonceStr();
        $signatureObj = new Signature();
        $signatureObj->add_data($cardExt['timestamp']);
        /*$couponApiTicket = $this->getCouponJSApiTicket();
        $signatureObj->add_data($couponApiTicket);*/
        $signatureObj->add_data('541f8e2562096a980c5b9e0986b2c397');
        $signatureObj->add_data($cardId);
        $signatureObj->add_data($cardExt['nonce_str']);
        $signature = $signatureObj->get_signature();
        $cardExt['signature'] = $signature;
        return decodeUnicodeToUTF8(json_encode($cardExt));
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    function getCouponJSApiTicket()
    {
        $requestUrl = WXCouponJSApi::GetCouponApiTicketUrl . getComponent_Access_Token();
        $couponApiTicket = requestWXServer($requestUrl);
        return $couponApiTicket['ticket'];
    }

    function test()
    {
        $signatureObj = new Signature();
        $signatureObj->add_data(1445132765);
        $signatureObj->add_data(555448841545);
        $signatureObj->add_data('E0o2-at6NcC2OsJiQTlwlF1DgX963jCd7Z5kVNhmb9luU79R5b6HC5t8XhR9z8WI5mdcgHJNgHUymkiOaLKbOA');
        $signatureObj->add_data('pL38Cs8wCih9UA1nvmsnW7YgAbu0');
        $signature = $signatureObj->get_signature();
        echo '<br>' . $signature;
    }
}

/**
 * 签名生成类
 * Class Signature
 */
class Signature
{
    /**
     *
     */
    function __construct()
    {
        $this->data = array();
    }

    /**
     * @param $str
     */
    function add_data($str)
    {
        array_push($this->data, (string)$str);
    }

    /**
     * @return string
     */
    function get_signature()
    {
        sort($this->data, SORT_STRING);
        return sha1(implode($this->data));
    }
}
