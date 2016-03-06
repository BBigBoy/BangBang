<?php
class Weixin_Coupon_Api
{
    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com';
    const LOGO_UPLOAD = '/cgi-bin/media/uploadimg?'; //以下API接口URL需要使用此前缀
    const USER_GET_CARDLIST = '/card/user/getcardlist?';
    ///卡券相关地址
    const CARD_CREATE = '/card/create?';
    const CARD_DELETE = '/card/delete?';
    const CARD_UPDATE = '/card/update?';
    const CARD_GET = '/card/get?';
    const CARD_BATCHGET = '/card/batchget?';
    const CARD_MODIFY_STOCK = '/card/modifystock?';
    const CARD_LOCATION_BATCHADD = '/card/location/batchadd?';
    const CARD_LOCATION_BATCHGET = '/card/location/batchget?';
    const CARD_GETCOLORS = '/card/getcolors?';
    const CARD_QRCODE_CREATE = '/card/qrcode/create?';
    const CARD_CODE_CONSUME = '/card/code/consume?';
    const CARD_CODE_DECRYPT = '/card/code/decrypt?';
    const CARD_CODE_GET = '/card/code/get?';
    const CARD_CODE_UPDATE = '/card/code/update?';
    const CARD_CODE_UNAVAILABLE = '/card/code/unavailable?';
    const CARD_TESTWHILELIST_SET = '/card/testwhitelist/set?';
    const CARD_MEMBERCARD_ACTIVATE = '/card/membercard/activate?';
    const CARD_MEMBERCARD_UPDATEUSER = '/card/membercard/updateuser?';      //激活会员卡
    const CARD_MOVIETICKET_UPDATEUSER = '/card/movieticket/updateuser?';    //更新会员卡
    const CARD_BOARDINGPASS_CHECKIN = '/card/boardingpass/checkin?';   //更新电影票(未加方法)
    const CARD_LUCKYMONEY_UPDATE = '/card/luckymoney/updateuserbalance?';     //飞机票-在线选座(未加方法)
    /**
     * @var string 授权公众号的appid，用于调用卡券相关api
     */
    private $authorizerAppid;//更新红包金额

    /**
     * @param $authorizerAppId string 授权公众号的appid，用于调用卡券相关api
     */
    function __construct($authorizerAppId)
    {
        $this->authorizerAppid = $authorizerAppId;
    }

    public function getUserCardList($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::USER_GET_CARDLIST, $data);
        return $returnContent;
    }

    /**
     * 向微信服务器请求数据，并处理返回结果
     * @param $appId string 授权公众号的APPID
     * @param $WHICHAPI string 具体请求的API地址
     * @param $data string 向微信服务器提交的数据
     * @return bool|mixed  为NULL或者错误代码，则返回false。  若为有效值则直接返回结果。
     */
    private static function requestWXServer($appId, $WHICHAPI, $data = null)
    {
        $authorizer_access_token = getAuthorizerAccessTokenByRefreshToken($appId);
        $post_url = self::API_BASE_URL_PREFIX . $WHICHAPI . 'access_token=' . $authorizer_access_token;
        if (is_array($data))
            $data = json_encode($data);
        //echo $data;
        $responseResult = http_request($post_url, $data);
        $returnContent = self::checkResult($responseResult);
        return $returnContent;
    }

    /**
     * 检查http_post的返回值。
     * @param $result string http_post的返回结果
     * @return bool|mixed 为NULL或者错误代码，则返回false。  若为有效值则直接返回结果。
     */
    private static function checkResult($result)
    {
        if ($result) {
            $json = json_decode($result, true);
            if (!$json || !empty($json['errcode'])) {
                /*$this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];*/
                return false;
            }
            return $result;
        }
        return false;
    }

    /**
     * 上传卡券logo图片，并获得图片在微信服务器中的地址
     * @param $logPicFileName string 卡券logo文件名（包含地址）
     * @return bool|string 成功返回图片地址，失败返回false
     */
    public function uploadLogo($logPicFileName)
    {
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizerAppid);
        $post_url = self::API_BASE_URL_PREFIX . self::LOGO_UPLOAD . 'access_token=' . $access_token;
        // $post_url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?accesstoken=' . $access_token;
        $varname = 'media';//上传到$_FILES数组中的 key
        $name = '2.jpg';
        $key = "$varname\"; filename=\"$name\r\n";
        $picContent = file_get_contents($logPicFileName);
        if ($picContent != false) {
            $fields[$key] = $picContent;
            $response = http_request($post_url, ($fields));
            $jsonObj = json_decode($response);
            $logo_url = (string)$jsonObj->url;
            return $logo_url;
        } else {
            return false;
        }
    }

    /**
     * 创建卡券
     * @param Array $data 卡券数据
     * @return array|boolean 返回数组中card_id为卡券ID
     */
    public function createCard($data)
    {
        $authorizer_access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizerAppid);
        $post_url = self::API_BASE_URL_PREFIX . self::CARD_CREATE . 'access_token=' . $authorizer_access_token;
        $responseResult = http_request($post_url, $data);
        return $responseResult;
    }

    /**
     * 更改卡券信息
     * 调用该接口更新信息后会重新送审，卡券状态变更为待审核。已被用户领取的卡券会实时更新票面信息。
     * @param string $data
     * @return boolean
     */
    public function updateCard($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_UPDATE, $data);
        return $returnContent;
    }

    /**
     * 删除卡券
     * 允许商户删除任意一类卡券。删除卡券后，该卡券对应已生成的领取用二维码、添加到卡包 JS API 均会失效。
     * 注意：删除卡券不能删除已被用户领取，保存在微信客户端中的卡券，已领取的卡券依旧有效。
     * @param string $card_id 卡券ID
     * @return boolean
     */
    public function delCard($card_id)
    {
        $data = array(
            'card_id' => $card_id,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_DELETE, $data);
        return $returnContent;
    }

    /**
     * 查询卡券详情
     * @param string $card_id
     * @return boolean|array    返回数组信息比较复杂，请参看卡券接口文档
     */
    public function getCardInfo($card_id)
    {
        $data = array(
            'card_id' => $card_id,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_GET, $data);
        return $returnContent;
    }

    /**
     * 获取颜色列表
     * 获得卡券的最新颜色列表，用于创建卡券
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardColors()
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_GETCOLORS);
        return $returnContent;
    }

    /**
     * 拉取门店列表
     * 获取在公众平台上申请创建的门店列表
     * @param int $offset 开始拉取的偏移，默认为0从头开始
     * @param int $count 拉取的数量，默认为0拉取全部
     * @return boolean|array   返回数组请参看 微信卡券接口文档 的json格式
     */
    public function getCardLocations($offset = 0, $count = 0)
    {
        $data = array(
            'offset' => $offset,
            'count' => $count
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_LOCATION_BATCHGET, $data);
        return $returnContent;
    }

    /**
     * 批量导入门店信息
     * @tutorial 返回插入的门店id列表，以逗号分隔。如果有插入失败的，则为-1，请自行核查是哪个插入失败
     * @param array $data 数组形式的json数据，由于内容较多，具体内容格式请查看 微信卡券接口文档
     * @return boolean|string 成功返回插入的门店id列表
     */
    public function addCardLocations($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_LOCATION_BATCHADD, $data);
        return $returnContent;
    }

    /**
     * 生成卡券二维码
     * 成功则直接返回ticket值，可以用 getQRUrl($ticket) 换取二维码url
     *
     * @param string $cardid 卡券ID 必须
     * @param string $code 指定卡券 code 码，只能被领一次。use_custom_code 字段为 true 的卡券必须填写，非自定义 code 不必填写。
     * @param string $openid 指定领取者的 openid，只有该用户能领取。bind_openid 字段为 true 的卡券必须填写，非自定义 openid 不必填写。
     * @param int $expire_seconds 指定二维码的有效时间，范围是 60 ~ 1800 秒。不填默认为永久有效。
     * @param boolean $is_unique_code 指定下发二维码，生成的二维码随机分配一个 code，领取后不可再次扫描。填写 true 或 false。默认 false。
     * @param string $balance 红包余额，以分为单位。红包类型必填（LUCKY_MONEY），其他卡券类型不填。
     * @return boolean|string
     */
    public function createCardQrcode($card_id, $code = '', $openid = '', $expire_seconds = 0, $is_unique_code = false, $balance = '')
    {
        $card = array(
            'card_id' => $card_id
        );
        if ($code)
            $card['code'] = $code;
        if ($openid)
            $card['openid'] = $openid;
        if ($expire_seconds)
            $card['expire_seconds'] = $expire_seconds;
        if ($is_unique_code)
            $card['is_unique_code'] = $is_unique_code;
        if ($balance)
            $card['balance'] = $balance;
        $data = array(
            'action_name' => "QR_CARD",
            'action_info' => array('card' => $card)
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_QRCODE_CREATE, $data);
        return $returnContent;
    }

    /**
     * 消耗 code
     * 自定义 code（use_custom_code 为 true）的优惠券，在 code 被核销时，必须调用此接口。
     *
     * @param string $code 要消耗的序列号
     * @param string $card_id 要消耗序列号所述的 card_id，创建卡券时use_custom_code 填写 true 时必填。
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "card":{"card_id":"pFS7Fjg8kV1IdDz01r4SQwMkuCKc"},
     *  "openid":"oFS7Fjl0WsZ9AMZqrI80nbIq8xrA"
     * }
     */
    public function consumeCardCode($code, $card_id = '')
    {
        $data = array('code' => $code);
        if ($card_id)
            $data['card_id'] = $card_id;
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_CODE_CONSUME, $data);
        return $returnContent;
    }

    /**
     * code 解码
     * @param string $encrypt_code 通过 choose_card_info 获取的加密字符串
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "code":"751234212312"
     *  }
     */
    public function decryptCardCode($encrypt_code)
    {
        $data = array(
            'encrypt_code' => $encrypt_code,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_CODE_DECRYPT, $data);
        return $returnContent;
    }

    /**
     * 查询 code 的有效性（非自定义 code）
     * @param string $code
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "openid":"oFS7Fjl0WsZ9AMZqrI80nbIq8xrA",    //用户 openid
     *  "card":{
     *      "card_id":"pFS7Fjg8kV1IdDz01r4SQwMkuCKc",
     *      "begin_time": 1404205036,               //起始使用时间
     *      "end_time": 1404205036,                 //结束时间
     *  }
     * }
     */
    public function checkCardCode($code)
    {
        $data = array(
            'code' => $code,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_CODE_GET, $data);
        return $returnContent;
    }

    /**
     * 批量查询卡列表
     * @param $offset int 开始拉取的偏移，默认为0从头开始
     * @param $count int  需要查询的卡片的数量（数量最大50,默认50）
     * @return boolean|array
     * {
     *  "errcode":0,
     *  "errmsg":"ok",
     *  "card_id_list":["ph_gmt7cUVrlRk8swPwx7aDyF-pg"],    //卡 id 列表
     *  "total_num":1                                       //该商户名下 card_id 总数
     * }
     */
    public function getCardIdList($offset = 0, $count = 50)
    {
        if ($count > 50)
            $count = 50;
        $data = array(
            'offset' => $offset,
            'count' => $count,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_BATCHGET, $data);
        return $returnContent;
    }

    /**
     * 更改 code
     * 为确保转赠后的安全性，微信允许自定义code的商户对已下发的code进行更改。
     * 注：为避免用户疑惑，建议仅在发生转赠行为后（发生转赠后，微信会通过事件推送的方式告知商户被转赠的卡券code）对用户的code进行更改。
     * @param string $code 卡券的 code 编码
     * @param string $card_id 卡券 ID
     * @param string $new_code 新的卡券 code 编码
     * @return boolean
     */
    public function updateCardCode($code, $card_id, $new_code)
    {
        $data = array(
            'code' => $code,
            'card_id' => $card_id,
            'new_code' => $new_code,
        );
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_CODE_UPDATE, $data);
        return $returnContent;
    }

    /**
     * 设置卡券失效
     * 设置卡券失效的操作不可逆
     * @param string $code 需要设置为失效的 code
     * @param string $card_id 自定义 code 的卡券必填。非自定义 code 的卡券不填。
     * @return boolean
     */
    public function unavailableCardCode($code, $card_id = '')
    {
        $data = array(
            'code' => $code,
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_CODE_UNAVAILABLE, $data);
        return $returnContent;
    }

    /**
     * 库存修改
     * @param string $data
     * @return boolean
     */
    public function modifyCardStock($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_MODIFY_STOCK, $data);
        return $returnContent;
    }

    /**
     * 激活/绑定会员卡
     * @param string $data 具体结构请参看卡券开发文档(6.1.1 激活/绑定会员卡)章节
     * @return boolean
     */
    public function activateMemberCard($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_MEMBERCARD_ACTIVATE, $data);
        return $returnContent;
    }

    /**
     * 会员卡交易
     * 会员卡交易后每次积分及余额变更需通过接口通知微信，便于后续消息通知及其他扩展功能。
     * @param string $data 具体结构请参看卡券开发文档(6.1.2 会员卡交易)章节
     * @return boolean|array
     */
    public function updateMemberCard($data)
    {
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_MEMBERCARD_UPDATEUSER, $data);
        return $returnContent;
    }

    /**
     * 更新红包金额
     * @param string $code 红包的序列号
     * @param $balance          红包余额
     * @param string $card_id 自定义 code 的卡券必填。非自定义 code 可不填。
     * @return boolean|array
     */
    public function updateLuckyMoney($code, $balance, $card_id = '')
    {
        $data = array(
            'code' => $code,
            'balance' => $balance
        );
        if ($card_id)
            $data['card_id'] = $card_id;
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_LUCKYMONEY_UPDATE, $data);
        return $returnContent;
    }

    /**
     * 设置卡券测试白名单
     * @param string $openid 测试的 openid 列表
     * @param string $user 测试的微信号列表
     * @return boolean
     */
    public function setCardTestWhiteList($openid = array(), $user = array())
    {
        $data = array();
        if (count($openid) > 0)
            $data['openid'] = $openid;
        if (count($user) > 0)
            $data['username'] = $user;
        $returnContent = self::requestWXServer($this->authorizerAppid, self::CARD_TESTWHILELIST_SET, $data);
        return $returnContent;
    }

}