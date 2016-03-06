<?php
/*
类，具体用法见示例。
注意填写的参数是int还是string或者微信卡包api SDK V1.0
!!README!!：
base_info的构造函数的参数是必填字段，有set接口的可选字段。
针对某一种卡的必填字段（参照文档）仍然需要手动set（比如团购券Groupon的deal_detail），通过card->get_card()拿到card的实体对象来set。
ToJson就能直接转换为符合规则的json。
Signature是方便生成签名的bool或者自定义class。
更具体用法见最后示例test，各种细节以最新文档为准。
*/
/**
 * 商品信息，目前只包含$quantity字段，表示库存数量
 * Class Sku
 */
class Sku
{
    /**
     * @param $quantity
     */
    function __construct($quantity)
    {
        $this->quantity = $quantity;
    }
}

;

/**
 * 使用日期及有效期信息
 * Class DateInfo
 */
class DateInfo
{
    /**
     * @param $type
     * @param $arg0
     * @param null $arg1
     */
    function __construct($type, $arg0, $arg1 = null)
    {
        if (!is_int($type))
            exit("DateInfo.type must be integer");
        $this->type = $type;
        if ($type == 1)  //固定日期区间
        {
            if (!is_int($arg0) || !is_int($arg1))
                exit("begin_timestamp and  end_timestamp must be integer");
            $this->begin_timestamp = $arg0;
            $this->end_timestamp = $arg1;
        } else if ($type == 2)  //固定时长（自领取后多少天内有效）
        {
            if (!is_int($arg0))
                exit("fixed_term must be integer");
            $this->fixed_term = $arg0;
        } else
            exit("DateInfo.tpye Error");
    }
}

;

/**
 * 卡券基本信息类。即所有类型卡券所共有的部分
 * Class BaseInfo
 */
class BaseInfo
{
    /**
     * @param $logo_url
     * @param $brand_name
     * @param $code_type
     * @param $title
     * @param $color
     * @param $notice
     * @param $service_phone
     * @param $description
     * @param $date_info
     * @param $sku
     */
    function __construct($logo_url, $brand_name, $code_type, $title, $color, $notice, $service_phone,
                         $description, $date_info, $sku)
    {
        if (!$date_info instanceof DateInfo)
            exit("date_info Error");
        if (!$sku instanceof Sku)
            exit("sku Error");
        if (!is_int($code_type))
            exit("code_type must be integer");
        $this->logo_url = $logo_url;
        $this->brand_name = $brand_name;
        $this->code_type = $code_type;
        $this->title = $title;
        $this->color = $color;
        $this->notice = $notice;
        $this->service_phone = $service_phone;
        $this->description = $description;
        $this->date_info = $date_info;
        $this->sku = $sku;
    }
    /**
     * @param $sub_title
     */
    function set_sub_title($sub_title)
    {
        $this->sub_title = $sub_title;
    }

    /**
     * @param $use_limit

    function set_use_limit($use_limit)
     * {
     * if (!is_int($use_limit))
     * exit("use_limit must be integer");
     * $this->use_limit = $use_limit;
     * }*/

    /**
     * @param $get_limit
     */
    function set_get_limit($get_limit)
    {
        if (!is_int($get_limit))
            exit("get_limit must be integer");
        $this->get_limit = $get_limit;
    }

    /**
     * @param $use_custom_code
     */
    function set_use_custom_code($use_custom_code)
    {
        $this->use_custom_code = $use_custom_code;
    }

    /**
     * @param $bind_openid
     */
    function set_bind_openid($bind_openid)
    {
        $this->bind_openid = $bind_openid;
    }

    /**
     * @param $can_share
     */
    function set_can_share($can_share)
    {
        $this->can_share = $can_share;
    }

    /**
     * @param $location_id_list
     */
    function set_location_id_list($location_id_list)
    {
        $this->location_id_list = $location_id_list;
    }

    /**
     * @param $url_name_type
     */
    function set_url_name_type($url_name_type)
    {
        if (!is_int($url_name_type))
            exit("url_name_type must be int");
        $this->url_name_type = $url_name_type;
    }

    /**
     * @param $custom_url
     */
    function set_custom_url($custom_url)
    {
        $this->custom_url = $custom_url;
    }
}

;

/**
 * 卡券基类，所有的卡券都是它的子类
 * Class CardBase
 */
class CardBase
{
    /**
     * @param $base_info
     */
    public function __construct($base_info)
    {
        $this->base_info = $base_info;
    }
}

;

/**
 * 通用券
 * Class GeneralCoupon
 */
class GeneralCoupon extends CardBase
{
    /**
     * @param $default_detail
     */
    function set_default_detail($default_detail)
    {
        $this->default_detail = $default_detail;
    }
}

;

/**
 * 团购卡券
 * Class Groupon
 */
class Groupon extends CardBase
{
    /**
     * @param $deal_detail
     */
    function set_deal_detail($deal_detail)
    {
        $this->deal_detail = $deal_detail;
    }
}

;

/**
 * 折扣券
 * Class Discount
 */
class Discount extends CardBase
{
    /**
     * @param $discount
     */
    function set_discount($discount)
    {
        $this->discount = $discount;
    }
}

;

/**
 * 礼品券
 * Class Gift
 */
class Gift extends CardBase
{
    /**
     * @param $gift
     */
    function set_gift($gift)
    {
        $this->gift = $gift;
    }
}

;

/**
 * 代金券
 * Class Cash
 */
class Cash extends CardBase
{
    /**
     * @param $least_cost
     */
    function set_least_cost($least_cost)
    {
        $this->least_cost = $least_cost;
    }

    /**
     * @param $reduce_cost
     */
    function set_reduce_cost($reduce_cost)
    {
        $this->reduce_cost = $reduce_cost;
    }
}

;

/**
 * 会员卡
 * Class MemberCard
 */
class MemberCard extends CardBase
{
    /**
     * @param $supply_bonus
     */
    function set_supply_bonus($supply_bonus)
    {
        $this->supply_bonus = $supply_bonus;
    }

    /**
     * @param $supply_balance
     */
    function set_supply_balance($supply_balance)
    {
        $this->supply_balance = $supply_balance;
    }

    /**
     * @param $bonus_cleared
     */
    function set_bonus_cleared($bonus_cleared)
    {
        $this->bonus_cleared = $bonus_cleared;
    }

    /**
     * @param $bonus_rules
     */
    function set_bonus_rules($bonus_rules)
    {
        $this->bonus_rules = $bonus_rules;
    }

    /**
     * @param $balance_rules
     */
    function set_balance_rules($balance_rules)
    {
        $this->balance_rules = $balance_rules;
    }

    /**
     * @param $prerogative
     */
    function set_prerogative($prerogative)
    {
        $this->prerogative = $prerogative;
    }

    /**
     * @param $bind_old_card_url
     */
    function set_bind_old_card_url($bind_old_card_url)
    {
        $this->bind_old_card_url = $bind_old_card_url;
    }

    /**
     * @param $activate_url
     */
    function set_activate_url($activate_url)
    {
        $this->activate_url = $activate_url;
    }
}

;

/**
 * 景点门票
 * Class ScenicTicket
 */
class ScenicTicket extends CardBase
{
    /**
     * @param $ticket_class
     */
    function set_ticket_class($ticket_class)
    {
        $this->ticket_class = $ticket_class;
    }

    /**
     * @param $guide_url
     */
    function set_guide_url($guide_url)
    {
        $this->guide_url = $guide_url;
    }
}

;

/**
 * 电影票
 * Class MovieTicket
 */
class MovieTicket extends CardBase
{
    /**
     * @param $detail
     */
    function set_detail($detail)
    {
        $this->detail = $detail;
    }
}

;
//飞机票、红包、会议门票暂未实现
/**
 * 卡券生成工厂类，所有卡券均通过此类生成
 * Class Card
 */
class Card
{  //工厂
    /**
     * @var array
     */
    private $CARD_TYPE = Array("GENERAL_COUPON",
        "GROUPON", "DISCOUNT",
        "GIFT", "CASH", "MEMBER_CARD",
        "SCENIC_TICKET", "MOVIE_TICKET");

    /**
     * @param $card_type
     * @param $base_info
     */
    function __construct($card_type, $base_info)
    {
        if (!in_array($card_type, $this->CARD_TYPE))
            exit("CardType Error");
        if (!$base_info instanceof BaseInfo)
            exit("base_info Error");
        $this->card_type = $card_type;
        switch ($card_type) {
            case $this->CARD_TYPE[0]:
                $this->general_coupon = new GeneralCoupon($base_info);
                break;
            case $this->CARD_TYPE[1]:
                $this->groupon = new Groupon($base_info);
                break;
            case $this->CARD_TYPE[2]:
                $this->discount = new Discount($base_info);
                break;
            case $this->CARD_TYPE[3]:
                $this->gift = new Gift($base_info);
                break;
            case $this->CARD_TYPE[4]:
                $this->cash = new Cash($base_info);
                break;
            case $this->CARD_TYPE[5]:
                $this->member_card = new MemberCard($base_info);
                break;
            case $this->CARD_TYPE[6]:
                $this->scenic_ticket = new ScenicTicket($base_info);
                break;
            case $this->CARD_TYPE[8]:
                $this->movie_ticket = new MovieTicket($base_info);
                break;
            default:
                exit("CardType Error");
        }
        return true;
    }

    /**
     * @return Cash|Discount|GeneralCoupon|Gift|Groupon|MemberCard|MovieTicket|ScenicTicket
     */
    function get_card()
    {
        switch ($this->card_type) {
            case $this->CARD_TYPE[0]:
                return $this->general_coupon;
            case $this->CARD_TYPE[1]:
                return $this->groupon;
            case $this->CARD_TYPE[2]:
                return $this->discount;
            case $this->CARD_TYPE[3]:
                return $this->gift;
            case $this->CARD_TYPE[4]:
                return $this->cash;
            case $this->CARD_TYPE[5]:
                return $this->member_card;
            case $this->CARD_TYPE[6]:
                return $this->scenic_ticket;
            case $this->CARD_TYPE[8]:
                return $this->movie_ticket;
            default:
                exit("GetCard Error");
        }
    }

    /**
     * @return string
     */
    function toJson()
    {
        return "{ \"card\":" . $this->decodeUnicodeToUTF8(json_encode($this)) . "}";
    }

    function decodeUnicodeToUTF8($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
            create_function(
                '$matches',
                'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
            ),
            $str);
    }
}

;

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

;
?>