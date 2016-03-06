<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/7/1
 * Time: 15:24
 */
namespace Platform\Common\WXStoreManage;
/**
 * Class Store
 * 这是门店生成类，此类对应的对象的属性就是要向微信服务器提交的数据
 * @package Platform\Common\WXStoreManage
 */
class Store
{
    /**构造函数中的变量是要向微信服务器提交的数据，请按照顺序及正确的格式书写
     * @param string $sid （非必须）自己自定义的sid，请自己确保唯一性，sid用于后续与获得的poi_id后的数据做对应
     * @param $business_name （必须填写）门店名称（仅为商户名，如：国美、麦当劳，不应包含地区、地址、分店名等信息，错误示例：北京国美）
     * @param $branch_name  （必须填写）分店名称（不应包含地区信息，不应与门店名有重复，错误示例：北京王府井店）
     * @param $province （必须填写）门店所在的省份（直辖市填城市名,如：北京市）
     * @param $city （必须填写）门店所在的城市
     * @param $district （必须填写）门店所在地区
     * @param $address （必须填写）门店所在的详细街道地址（不要填写省市信息）
     * @param $telephone（必须填写） 门店的电话（纯数字，区号、分机号均由“-”隔开）
     * @param $offset_type（必须填写） 坐标类型，1 为火星坐标（目前只能选1）
     * @param $longitude （必须填写）门店所在地理位置的经度
     * @param $latitude （必须填写）门店所在地理位置的纬度（经纬度均为火星坐标，最好选用腾讯地图标记的坐标）
     * @param string $recommend （非必须）推荐品，餐厅可为推荐菜；酒店为推荐套房；景点为推荐游玩景点等，针对自己行业的推荐内容
     * @param $special （必须填写）特色服务，如免费wifi，免费停车，送货上门等商户能提供的特色功能或服务
     * @param string $introduction（必非须） 商户简介，主要介绍商户信息等
     * @param $open_time （必须填写）营业时间，24 小时制表示，用“-”连接，如 8:00-20:00
     * @param int $avg_price （必须填写）人均价格，大于0 的整数
     */
    function __construct($sid = '', $business_name, $branch_name, $province, $city,
                         $district, $address, $telephone, $offset_type,
                         $longitude, $latitude,$recommend = '', $special,
                         $introduction = '', $open_time, $avg_price = 0)
    {
        if (is_int($sid))
            $this->sid = $sid;
        $this->business_name = $business_name;
        $this->branch_name = $branch_name;
        $this->province = $province;
        $this->city = $city;
        $this->district = $district;
        $this->address = $address;
        $this->telephone = $telephone;
        $this->offset_type = $offset_type;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->recommend = $recommend;
        $this->special = $special;
        $this->introduction = $introduction;
        $this->open_time = $open_time;
        $this->avg_price = $avg_price;
    }

    /**
     * 这是返回微信服务器要求格式的门店的类型，请严格按照要求填写
     * 门店的类型（不同级分类用“,”隔开，如：美食，川菜，火锅。详细分类参见附件：微信门店类目表）
     * @param $data1
     * @param $data2
     * @param string $data3
     */
    public function set_categories($data1, $data2, $data3 = '')
    {
        if ($data3 == '') {
            $categories = array($data1 . ',' . $data2);
            $this->categories=$categories;
        } else {
            $categories = array($data1 . ',' . $data2 . ',' . $data3);
           $this->categories=$categories;
        }
    }

    /**
     * 这个方法用于返回微信服务器要求格式的门店图片URL
     * @param $image_url_str  此参数要填写的是字符串形式的URL，URL必须由上传微信服务器的图片返回的URL，
     * 多个URL的时候，请用逗号（半角）隔开
     */
    public function set_photo_list($image_url_str)
    {
        $att = explode(',', $image_url_str);
        $photo_list = array();
        foreach ($att as $key => $value) {
            $photo_list[] = array('photo_url' => $value);
        }
         $this->photo_list=$photo_list;
    }
}

