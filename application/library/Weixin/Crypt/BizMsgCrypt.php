<?php
/**
 * 对开放平台发送给开放账号的消息加解密示例代码.
 *
 * @copyright Copyright (c) 1998-2014 Tencent Inc.
 */

/**
 * 1.第三方回复加密消息给开放平台；
 * 2.第三方收到开放平台发送的消息，验证消息的安全性，并对消息进行解密。
 */
class Weixin_Crypt_BizMsgCrypt
{
    private $token;
    private $encodingAesKey;
    private $appId;

    /**
     * 构造函数
     *
     * @param  $token string 开放平台上，开发者设置的token
     * @param  $encodingAesKey string 开放平台上，开发者设置的EncodingAESKey
     * @param  $appId string 开放平台的appId
     */
    function __construct($token, $encodingAesKey, $appId)
    {
        $this->token = $token;
        $this->appId = $appId;
        $this->encodingAesKey = $encodingAesKey;
    }

    public function toString()
    {
        echo '<br>$token' . $this->token;
        echo '<br>$encodingAesKey' . $this->encodingAesKey;
        echo '<br>$appId' . $this->appId;
    }

    /*
        public function WXBizMsgCrypt($token, $encodingAesKey, $appId)
        {
            $this->token = $token;
            $this->encodingAesKey = $encodingAesKey;
            $this->appId = $appId;
        }*/

    /**
     * 将开放平台回复用户的消息加密打包.
     * <ol>
     *       <li>对要发送的消息进行AES-CBC加密</li>
     *       <li>生成安全签名</li>
     *       <li>将消息密文和安全签名打包成xml格式</li>
     * </ol>
     *
     * @param  $replyMsg string 开放平台待回复用户的消息，xml格式的字符串
     * @param  $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param  $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param  &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
     *                         当return返回0时有效
     * @return int 成功0，失败返回对应的错误码
     */
    public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg)
    {
        $pc = new Weixin_Crypt_Prpcrypt($this->encodingAesKey);
        // 加密
        $array = $pc->encrypt($replyMsg, $this->appId);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        if ($timeStamp == null) {
            $timeStamp = time();
        }
        $encrypt = $array[1];
        // 生成安全签名
        $sha1 = new SHA1;
        $array = $sha1->getSHA1($this->token, $timeStamp, $nonce, $encrypt);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];
        // 生成发送的xml
        $xmlparse = new Weixin_Crypt_XMLParse;
        $encryptMsg = $xmlparse->generate($encrypt, $signature, $timeStamp, $nonce);
        return Weixin_Crypt_ErrorCode::$OK;
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     * <ol>
     *       <li>利用收到的密文生成安全签名，进行签名验证</li>
     *       <li>若验证通过，则提取xml中的加密消息</li>
     *       <li>对消息进行解密</li>
     * </ol>
     *
     * @param  $msgSignature string 签名串，对应URL参数的msg_signature
     * @param  $timestamp string 时间戳 对应URL参数的timestamp
     * @param  $nonce string 随机串，对应URL参数的nonce
     * @param  $postData string 密文，对应POST请求的数据
     * @param  &$msg string 解密后的原文，当return返回0时有效
     * @param  $elementName string 标识解密的消息类型，默认为微信转发的用户消息，当设置为'AppId'时，为微信服务器推送的事件信息
     * @return int 成功0，失败返回对应的错误码
     */
    public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg, $elementName = 'ToUserName')
    {
        if (strlen($this->encodingAesKey) != 43) {
            return Weixin_Crypt_ErrorCode::$IllegalAesKey;
        }

        $pc = new Weixin_Crypt_Prpcrypt($this->encodingAesKey);
        // 提取密文
        $xmlparse = new Weixin_Crypt_XMLParse;
        $array = $xmlparse->extract($postData, $elementName);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        if ($timestamp == null) {
            $timestamp = time();
        }

        $encrypt = $array[1];
        //$touser_name = $array[2];
        // 验证安全签名
        $sha1 = new Weixin_Crypt_SHA1();
        $array = $sha1->getSHA1($this->token, $timestamp, $nonce, $encrypt);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $msgSignature) {
            return Weixin_Crypt_ErrorCode::$ValidateSignatureError;
        }
        $result = $pc->decrypt($encrypt, $this->appId);
        if ($result[0] != 0) {
            return $result[0];
        }

        $msg = $result[1];
        return Weixin_Crypt_ErrorCode::$OK;
    }
}

