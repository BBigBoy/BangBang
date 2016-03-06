<?php
namespace Platform\Common\WXPay;
/*
 * 微信支付公共配置参数信息类
 */
class WXPayConf{
    /*
     * 微信公众号配置信息
     */
    const APPID = "wx477688baf3a4a9f6";
    const MCHID = "1231704402";
    const KEY = "gFlGj6h4y9I8koc8bv0mX7qtW2e5vHi5";
    const SECRET = "76e0389fc1f7ed9d6f49859b455dfd39";
    /*
     * 证书路径设置
     */
    const SSLCERT_PATH = "./Public/WeiXinPayCert/apiclient_cert.pem";
    const SSLKEY_PATH = "./Public/WeiXinPayCert/apiclient_key.pem";
    const SSLCA_PATH = "./Public/WeiXinPayCert/rootca.pem";
    /*
     * JSAPI支付后台通知接受地址
     */
    const JSAPI_NOTIFY_URL = "http://182.92.68.92/wjsh/weixinpay/NOTIFY_URL/JSAPI_NOTIFY_URL.php";
    /*
     * 扫码支付接收后台通知
     */
    const SCANCODE_NOTIFY_URL ="http://182.92.68.92/wjsh/weixinpay/NOTIFY_URL/SCAN_NOTIFY_URL.php";
}

?>