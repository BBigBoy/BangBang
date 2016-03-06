<?php

/**
 * Class StoreManage
 * @package Platform\Common\WXStoreManage
 *门店管理类及相关方法
 * 方法有：上传门店图片方法uploadLogo()，
 * 创建门店方法creatStore(),
 * 查询单个门店方法searchSingleStore($poi_id)，
 *查询门店列表方法searchStoreList($begin, $limit)，
 * 更新门店信息方法updateStore($poi, $telephone = '',
 * $image_url_str = '',$recommend = '', $special = '', $introduction = '',
 * $open_time = '', $avg_price = '')，
 * 删除门店方法deleteStore($poi_id)，
 * 获得门店类目信息getWXCategory()
 */
class Weixin_StoreManage_StoreManage
{
    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com';
    const LOGO_UPLOAD = '/cgi-bin/media/uploadimg?';
    const ADD_POI = '/cgi-bin/poi/addpoi?';
    const SEARCH_SINGLE_STORE = '/cgi-bin/poi/getpoi?';
    const SEARCH_STORE_LIST = '/cgi-bin/poi/getpoilist?';
    const UPDATA_STORE = '/cgi-bin/poi/updatepoi?';
    const DELETE_STORE = '/cgi-bin/poi/delpoi?';
    const GET_WXCATEGORY_LIST = '/cgi-bin/api_getwxcategory?';

    private $authorizedAppId = '';

    /**
     * @param $authorizedAppId string 授权公众账号的appId
     */
    function __construct($authorizedAppId)
    {
        $this->authorizedAppId = $authorizedAppId;
    }

    /**
     * 上传门店logo图片，并获得图片在微信服务器中的地址
     * @param $logPicFileName string 卡券logo文件名（包含地址）
     * @return bool|string 成功返回图片地址，失败返回false
     */
    public function uploadLogo()
    {
        $logPicFileName = 'http://tpplatform-poi.stor.sinaapp.com/Uploads/5594f974be422.jpg';
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::LOGO_UPLOAD . 'access_token=' . $access_token;
        //echo $post_url;
        $varname = 'media';//上传到$_FILES数组中的 key
        $type = 'image/jpg';
        $name = '2.jpg';
        $key = "$varname\"; filename=\"$name\r\nContent-Type: $type\r\n";
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
     *
     * 这是申请门店的接口
     * 该方法已经提前设置好演示参数，具体参数请查看Store类的文档
     *
     * @param $data array 向微信服务器提交的数据
     * @return bool|mixed  若成功返回一个数组
     */
    public function creatStore()
    {
        $storeObj = new Weixin_StoreManage_Store(333788,
            '麦当劳', '艺苑路店', '广东省', '广州市', '珠海区', '怡园路11号', '020-12345678', (int)1, '115.32375', '25.097486',
            '麦辣鸡腿套餐，麦乐鸡，全家桶', '免费WiFi，外卖服务',
            '麦当劳是全球大型跨国连锁餐厅，1940 年创立于美国，在世界上
大约拥有3 万间分店。主要售卖汉堡包，以及薯条、炸鸡、汽水、冰品、沙拉、水果等
快餐食品', '8:00-20:00', '35');
        $storeObj->set_categories('美食', '小吃快餐');
        $storeObj->set_photo_list("http://mmbiz.qpic.cn/mmbiz/ibrHZxWsXD4aiaYbx6VAFthIJudMQmhVJkibUsgI5WV3eRBIwsvCjm86zIg5VDImVyIIVmcOCQMkGKgficuEKaKDJA/0");
        $data = array(
            'business' => array(
                'base_info' => $storeObj
            )
        );
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::ADD_POI . 'access_token=' . $access_token;
        $returnContent = requestWXServer($post_url, decodeUnicodeToUTF8(json_encode($data)));
        var_dump($returnContent);
    }

    /**
     * 这是查询单个门店信息的方法
     * @param $poi_id
     */
    public function searchSingleStore($poi_id)
    {
        $data = array(
            'poi_id' => $poi_id
        );
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::SEARCH_SINGLE_STORE . 'access_token=' . $access_token;
        echo $post_url . json_encode($data);//die();
        //decodeUnicodeToUTF8(json_encode($data)
        $returnContent = requestWXServer($post_url, $data);
        var_dump($returnContent);
    }

    /**
     * 这是查询门店列表的方法
     * 商户可以通过该接口，批量查询自己名下的门店list，
     * 并获取已审核通过的poi_id（所有状态均会返回poi_id，但该poi_id不一定为最终id）、
     * 商户自身sid 用于对应、商户名、分店名、地址字段
     * @param $begin int 开始位置，0 即为从第一条开始查询
     * @param $limit int 返回数据条数，最大允许50，默认为20
     */
    public function searchStoreList($begin, $limit)
    {
        $data = array(
            'begin' => $begin,
            'limit' => $limit
        );
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::SEARCH_STORE_LIST . 'access_token=' . $access_token;
        echo $post_url . json_encode($data);//die();
        //decodeUnicodeToUTF8(json_encode($data)
        $returnContent = requestWXServer($post_url, $data);
        var_dump($returnContent);
    }

    /**
     * 这是修改门店信息的方法
     * 商户可以通过该接口，修改门店的服务信息，包括：图片列表、营业时间、
     * 推荐、特色服务、简介、人均价格、电话7 个字段（名称、坐标、地址等不可修改）
     * 修改后需要人工审核。
     * @param $poi_id
     * @param string $telephone 门店的电话（纯数字，区号、分机号均由“-”隔开）如：020-12345678
     * @param string $image_url_str 门店的图片URL，此URL必须由本类的uploadLogo()方法生成
     * @param string $recommend 推荐品，餐厅可为推荐菜；酒店为推荐套房；景点为推荐游玩景点等，
     * 针对自己行业的推荐内容。如"麦辣鸡腿堡套餐，麦乐鸡，全家桶"
     * @param string $special 特色服务，如免费wifi，免费停车，送货上门等商户能提供的特色功能或服务
     * @param string $introduction 商户简介，主要介绍商户信息等
     * @param string $open_time 营业时间，24 小时制表示，用“-”连接，如 8:00-20:00
     * @param string $avg_price 人均价格，大于0 的整数
     */
    public function updateStore($poi_id, $telephone = '', $image_url_str = '',
                                $recommend = '', $special = '', $introduction = '',
                                $open_time = '', $avg_price = '')
    {
        $att = explode(',', $image_url_str);
        $photo_list = array();
        foreach ($att as $key => $value) {
            $photo_list[] = array('photo_url' => $value);
        }
        $this->photo_list = $photo_list;
        $data = array(
            'business' => array(
                'base_info' => array(
                    'poi_id' => $poi_id,
                    'telephone' => $telephone,
                    'photo_list' => $photo_list,
                    'recommend' => $recommend,
                    'special' => $special,
                    'introduction' => $introduction,
                    'open_time' => $open_time,
                    'avg_price' => $avg_price,
                )
            )
        );
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self:: UPDATA_STORE . 'access_token=' . $access_token;
        echo $post_url . json_encode($data);
        $returnContent = requestWXServer($post_url, $data);
        var_dump($returnContent);
    }

    /**
     *这是删除门店的方法
     * @param $poi_id
     */
    public function deleteStore($poi_id)
    {
        $data = array(
            'poi_id' => $poi_id
        );
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::DELETE_STORE . 'access_token=' . $access_token;
        echo $post_url . json_encode($data);//die();
        //decodeUnicodeToUTF8(json_encode($data)
        $returnContent = requestWXServer($post_url, $data);
        var_dump($returnContent);
    }

    /**
     *这是获得门店类目表的方法
     */
    public function getWXCategory()
    {
        $data = null;
        $access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . self::GET_WXCATEGORY_LIST . 'access_token=' . $access_token;
        echo $post_url . json_encode($data);
        $returnContent = requestWXServer($post_url, $data);
        var_dump($returnContent);
    }

    /**
     * 同步所有门店列表，包括
     * @return mixed
     */
    public function syncPoiList()
    {
        $data['begin'] = 0;
        //$data['limit']的值最大为50
        $data['limit'] = 50;
        $post_url = 'https://api.weixin.qq.com/cgi-bin/poi/getpoilist?access_token=' . getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $poiInfoAtt = requestWXServer($post_url, json_encode($data));
        $businessList = array();
        if ($poiInfoAtt) {
            $businessList = $poiInfoAtt['business_list'];
        }
        $totalPoiNum = (int)$poiInfoAtt['total_count'];
        $getPoiNum = 50;
        while ($totalPoiNum > $getPoiNum) {
            $data['begin'] = $getPoiNum + 1;
            $getPoiNum += 50;
            $poiInfoAtt = requestWXServer($post_url, json_encode($data));
            if ($poiInfoAtt) {
                $businessList = array_merge($businessList, $poiInfoAtt['business_list']);
            }
        }
        $poiList = array();
        foreach ($businessList as $key => $value) {
            $value['base_info']['photo_list'] = json_encode($value['base_info']['photo_list']);
            $value['base_info']['categories'] = decodeUnicodeToUTF8(json_encode($value['base_info']['categories']));
            $poiList[] = $value['base_info'];
        }
        $poiModel = new Weixin_PoiModel($this->authorizedAppId);
        $delState = $poiModel->delAll();
        if ($delState !== false) {
            if ($poiList) {
                $addState = $poiModel->addAll($poiList);
                if ($addState === false) {
                    errorLog(json_encode($poiList));
                    return false;
                }
            }
            return true;
        } else {
            errorLog(__METHOD__, __LINE__);
            return false;
        }
    }

    /**
     * 获取审核通过的门店列表
     * @return mixed 成功返回结果，失败返回false
     */
    public function getAvailablePoiList()
    {
        $poiModel = new Weixin_PoiModel($this->authorizedAppId);
        $wherePoi['available_state'] = 3;
        $availablePoiList = $poiModel->findMultiPoi($wherePoi);
        if (is_array($availablePoiList)) {
            return $availablePoiList;
        } else {
            errorLog(__METHOD__, __LINE__);
            return false;
        }
    }

    /**
     * 获取审核通过的门店名称列表
     * @return mixed 成功返回结果，失败返回false
     */
    public function getAvailablePoiNameList()
    {
        $poiModel = new Weixin_PoiModel($this->authorizedAppId);
        $wherePoi['available_state'] = 3;
        $availablePoiList = $poiModel->findMultiPoi($wherePoi, 'business_name,branch_name,poi_id');
        if (is_array($availablePoiList)) {
            $poiList[0]['name'] = '全部门店';
            foreach ($availablePoiList as &$value) {
                $branch_name = '';
                if ($value['branch_name'] != "") {
                    $branch_name = '(' . $value['branch_name'] . ')';
                }
                $poiList[$value['poi_id']]['name'] = $value['business_name'] . $branch_name;
            }
            return $poiList;
        } else {
            errorLog(__METHOD__, __LINE__);
            return false;
        }
    }
}