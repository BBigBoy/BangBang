<?php
class Weixin_Chat_WeChat
{
    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';
    const EVENT_SUBSCRIBE = 'subscribe';       //订阅
    const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
    const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
    const EVENT_LOCATION = 'LOCATION';         //上报地理位置
    const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
    const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
    const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
    const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
    const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
    const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
    const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
    const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券

    private $_revAtt;
    private $_msg;
    private $_text_filter = true;
    private $encrypt_type;

    public function __construct($options)
    {
        $this->initMsgObj();
    }

    private function initMsgObj()
    {
        $msg_signature = $_GET['msg_signature'];
        $timeStamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
        $postStr = file_get_contents("php://input");
        $pc = new Weixin_Crypt_BizMsgCrypt(C('TOKEN'), C('ENCODING_AES_KEY'), C('APP_ID'));
        $errCode = $pc->decryptMsg($msg_signature, $timeStamp, $nonce, $postStr, $msg);
        if ($errCode == 0) {
            $this->_revAtt = (array)simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType()
    {
        if (isset($this->_revAtt['MsgType']))
            return $this->_revAtt['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID()
    {
        if (isset($this->_revAtt['MsgId']))
            return $this->_revAtt['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevTime()
    {
        if (isset($this->_revAtt['CreateTime']))
            return $this->_revAtt['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent()
    {
        if (isset($this->_revAtt['Content']))
            return $this->_revAtt['Content'];
        else if (isset($this->_revAtt['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->_revAtt['Recognition'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic()
    {
        if (isset($this->_revAtt['PicUrl']))
            return array(
                'mediaid' => $this->_revAtt['MediaId'],
                'picurl' => (string)$this->_revAtt['PicUrl'],    //防止picurl为空导致解析出错
            );
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink()
    {
        if (isset($this->_revAtt['Url'])) {
            return array(
                'url' => $this->_revAtt['Url'],
                'title' => $this->_revAtt['Title'],
                'description' => $this->_revAtt['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo()
    {
        if (isset($this->_revAtt['Location_X'])) {
            return array(
                'x' => $this->_revAtt['Location_X'],
                'y' => $this->_revAtt['Location_Y'],
                'scale' => $this->_revAtt['Scale'],
                'label' => $this->_revAtt['Label']
            );
        } else
            return false;
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo()
    {
        if (isset($this->_revAtt['Latitude'])) {
            return array(
                'x' => $this->_revAtt['Latitude'],
                'y' => $this->_revAtt['Longitude'],
                'precision' => $this->_revAtt['Precision'],
            );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent()
    {
        if (isset($this->_revAtt['Event'])) {
            $array['event'] = $this->_revAtt['Event'];
        }
        if (isset($this->_revAtt['EventKey'])) {
            $array['key'] = $this->_revAtt['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     * @return $this
     */
    public function text($text = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_TEXT,
            'Content' => $this->_auto_text_filter($text),
            'CreateTime' => time(),
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 获取消息发送者
     */
    public function getRevFrom()
    {
        if (isset($this->_revAtt['FromUserName']))
            return $this->_revAtt['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo()
    {
        if (isset($this->_revAtt['ToUserName']))
            return $this->_revAtt['ToUserName'];
        else
            return false;
    }

    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    private function _auto_text_filter($text)
    {
        if (!$this->_text_filter) return $text;
        return str_replace("\r\n", "\n", $text);
    }

    /**
     * 设置发送消息
     * @param array|string $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     * @return array|string
     */
    public function Message($msg = '', $append = false)
    {
        if (is_null($msg)) {
            $this->_msg = array();
        } elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg, $msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }

    /**
     * 设置回复消息
     * Example: $obj->image('media_id')->reply();
     * @param string $mediaid
     * @return $this
     */
    public function image($mediaid = '')
    {
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_IMAGE,
            'Image' => array('MediaId' => $mediaid),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->voice('media_id')->reply();
     * @param string $mediaid
     * @return $this
     */
    public function voice($mediaid = '')
    {
        //$FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VOICE,
            'Voice' => array('MediaId' => $mediaid),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->video('media_id','title','description')->reply();
     * @param string $mediaid
     * @return $this
     */
    public function video($mediaid = '', $title = '', $description = '')
    {

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VIDEO,
            'Video' => array(
                'MediaId' => $mediaid,
                'Title' => $title,
                'Description' => $description
            ),
            'CreateTime' => time()
        );
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     * @return $this
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '')
    {

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => self::MSGTYPE_MUSIC,
            'Music' => array(
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            )
        );
        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }
        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *    "0"=>array(
     *        'Title'=>'msg title',
     *        'Description'=>'summary text',
     *        'PicUrl'=>'http://www.domain.com/1.jpg',
     *        'Url'=>'http://www.domain.com/1.html'
     *    ),
     *    "1"=>....
     *  )
     * @return $this
     */
    public function news($newsData = array())
    {

        $count = count($newsData);

        $msg = array(
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_NEWS,
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData
        );
        $this->Message($msg);
        return $this;
    }


    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param array $msg 要发送的信息, 默认取$this->_msg
     * @return bool
     */
    public function reply($msg = array())
    {
        if (empty($msg)) {
            if (empty($this->_msg))   //防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            $msg = $this->_msg;
        }
        $Weixin_Crypt_XMLParse = new Weixin_Crypt_XMLParse();
        $xmlData = $Weixin_Crypt_XMLParse->xml_encode($msg);
        if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
            $timestamp = time();
            $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $pc = new Weixin_Crypt_BizMsgCrypt(C('TOKEN'), C('ENCODING_AES_KEY'), C('APP_ID'));
            $encryptXMLData = '';
            $encryptState = $pc->encryptMsg($xmlData, $timestamp, $nonce, $encryptXMLData);
            if ($encryptState === 0) {
                $xmlData = $encryptXMLData;
            } else {
                errorLog('encrypt fail------>' . $xmlData);
            }
        }
        echo $xmlData;

    }

}