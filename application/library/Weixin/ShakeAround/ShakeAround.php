<?php
namespace Platform\Common\WXShakeAround;

use Common\Common\ParamValidate;

load('Platform.WXOpenplatform');

/**
 * Class ShakeAround  摇一摇周边类，包含相关方法
 * 这里面的方法其实可以归纳为switch...case结构，但是为了清晰表述不同接口，以方法形式展现。
 */
class ShakeAround
{
    ///微信摇一摇周边
    const API_BASE_URL_PREFIX = 'https://api.weixin.qq.com/shakearound';//以下API接口URL需要使用此前缀
    const SHAKEAROUND_DEVICE_APPLYID = '/device/applyid?';//申请设备ID
    const SHAKEAROUND_DEVICE_APPLYSTATUS = '/device/applystatus?';//查询设备申请状态
    const SHAKEAROUND_DEVICE_UPDATEINFO = '/device/update?';//编辑设备信息
    const SHAKEAROUND_DEVICE_BINDLOCATION = '/device/bindlocation?';//配置设备与门店ID的关系
    const SHAKEAROUND_DEVICE_SEARCH = '/device/search?';//查询设备列表
    const SHAKEAROUND_DEVICE_BINDPAGE = '/device/bindpage?';//配置设备与页面的绑定关系
    const SHAKEAROUND_MATERIAL_ADD = '/material/add?';//上传图片素材
    const SHAKEAROUND_PAGE_ADD = '/page/add?';//增加页面
    const SHAKEAROUND_PAGE_UPDATE = '/page/update?';//编辑页面
    const SHAKEAROUND_PAGE_SEARCH = '/page/search?';//查询页面列表
    const SHAKEAROUND_PAGE_DELETE = '/page/delete?';//删除页面
    const SHAKEAROUND_USER_GETSHAKEINFO = '/user/getshakeinfo?';//获取摇周边的设备及用户信息
    const SHAKEAROUND_STATISTICS_DEVICE = '/statistics/device?';//以设备为维度的数据统计接口
    const SHAKEAROUND_STATISTICS_PAGE = '/statistics/page?';//以页面为维度的数据统计接口
    const SHAKEAROUND_SEARCH_RELATION = '/relation/search?';//查询设备与页面的关联关系
    ///H5获取周边设备信息
    const SHAKEAROUND_DEVICE_GROUP_ADD = '/device/group/add?';//添加设备分组
    const SHAKEAROUND_DEVICE_GROUP_UPDATE = '/device/group/update?';//更新设备分组
    const SHAKEAROUND_DEVICE_GROUP_DELETE = '/device/group/delete?';//删除分组
    const SHAKEAROUND_DEVICE_GROUP_GET_LIST = '/device/group/getlist?';//查询账号下所有的分组
    const SHAKEAROUND_DEVICE_GROUP_GET_DETAIL = '/device/group/getdetail?';//查询分组详情，包括分组名，分组id，分组里的设备列表。
    const SHAKEAROUND_DEVICE_GROUP_DEVICE_ADD = '/device/group/adddevice?';//添加设备到分组
    const SHAKEAROUND_DEVICE_GROUP_DEVICE_DELETE = '/device/group/deletedevice?';//从分组中移除设备
    ///
    /**
     * @var string 授权的第三方公众号APPID
     */
    private $authorizedAppId;
    private static $_instance;

    /**
     * @param $authorizedAppId string 授权公众账号的appId
     * @return ShakeAround
     */
    public static function getInstance($authorizedAppId)
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new self($authorizedAppId);
        }
        return self::$_instance;
    }

    /**
     * @param $authorizedAppId string 授权公众账号的appId
     */
    private function __construct($authorizedAppId)
    {
        $this->authorizedAppId = $authorizedAppId;
    }

    /**
     * [applyShakeAroundDevice 申请配置设备所需的UUID、Major、Minor。
     * 若激活率小于50%，不能新增设备。单次新增设备超过500个，需走人工审核流程。
     * 审核通过后，可用返回的批次ID 用“查询设备列表”接口拉取本次申请的设备ID。]
     * @param $data array
     * @return boolean|mixed
     * {"data": {"apply_id": 123,"audit_status": 0,"audit_comment": "审核未通过"
     * },"errcode": 0,"errmsg": "success."}
     * audit_status    审核状态。0：审核未通过、1：审核中、2：审核已通过；若单次申请的设备ID数量小于等于500个，系统会进行快速审核；若单次申请的设备ID数量大于500个，会在三个工作日内完成审核
     * audit_comment    审核备注，包括审核不通过的原因
     * apply_id    申请的批次ID，可用在“查询设备列表”接口按批次查询本次申请成功的设备ID
     */
    public function applyDevice($data)
    {
        /* $data 参数
        quantity int 申请的设备数量
        apply_reason string 设备申请理由（不超过100个字）
        comment string 申请备注信息（NULL）（不超过15个汉字或30个英文字母）
        poi_id int 设备关联的门店ID（NULL）*/
        $valiRuleArr['quantity'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['apply_reason'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 100);
        $valiRuleArr['comment'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::IS_NULL => true, ParamValidate::MAX_LEN => 30);
        $valiRuleArr['poi_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR, ParamValidate::IS_NULL => true);
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_APPLYID, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 调用摇一摇接口
     * @param $interfaceName string 摇周边接口名称
     * @param $requestData array 调用接口的请求数据
     * @param $is_file bool 标识当前调用是否为文件上传，默认为false
     * @return boolean|mixed  成功返回相关数据（数组），失败返回false
     */
    private function invokeShakeInterface($interfaceName, $requestData = array(), $is_file = false)
    {
        $authorizer_access_token = getAuthorizerAccessTokenByRefreshToken($this->authorizedAppId);
        $post_url = self::API_BASE_URL_PREFIX . $interfaceName . 'access_token=' . $authorizer_access_token;
        $returnContent = requestWXServer($post_url, $requestData, $is_file);
        return $returnContent;
    }

    /**
     * 查询设备申请状态
     * @param $data array 包含一个元素，设备申请批次ID
     * @return bool|mixed
     */
    public function checkDeviceApplyState($data)
    {
        $valiRuleArr['apply_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_APPLYSTATUS, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 编辑设备信息。修改设备备注信息
     *
     * @param $comment string 设备备注信息
     * @param $device_id int 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param $uuid string 三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param $major int
     * @param $minor int
     * @return bool|mixed
     */
    public function updateDeviceComment($comment = '', $device_id = 0, $uuid = '', $major = 0, $minor = 0)
    {
        $valiRuleArr['comment'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 30, ParamValidate::IS_NULL => true);
        $device_identifier = self::getDeviceIdentifier($device_id, $uuid, $major, $minor);
        $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
            array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
        $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
        $valiRuleArr['device_identifier'] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
        , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        $data = array(
            'device_identifier' => $device_identifier,
            'comment' => $comment
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_UPDATEINFO, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 获得设备标识。  指定一个摇一摇设备，可以通过单独的$device_id唯一确认，同时也可以使用$uuid, $major, $minor三者来指定一个设备
     * @param string $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param string $major
     * @param string $minor
     * @return array|bool 返回可以唯一确定设备的标识。  否则返回false
     */
    private static function getDeviceIdentifier($device_id, $uuid, $major, $minor)
    {
        if ($device_id == 0) {
            if (!$uuid || !$major || !$minor) {
                return false;
            }
            $device_identifier = array(
                'uuid' => $uuid,
                'major' => (int)$major,
                'minor' => (int)$minor
            );
        } else {
            $device_identifier = array(
                'device_id' => (int)$device_id
            );
        }
        return $device_identifier;
    }

    /**
     * [searchShakeAroundDevice 查询已有的设备ID、UUID、Major、Minor、激活状态、备注信息、关联门店、关联页面等信息。
     * 可指定设备ID 或完整的UUID、Major、Minor 查询，也可批量拉取设备信息列表。]
     * @param array $data
     * @return boolean|mixed
     * 查询指定设备时：
     * {"type": 1,"device_identifiers":[{"device_id":10100,
     * "uuid":"FDA50693-A4E2-4FB1-AFCF-C6EB07647825",
     * "major":10001,"minor":10002}]}
     * 需要分页查询或者指定范围内的设备时：{"type": 2,"begin": 0,"count": 3}
     * 当需要根据批次ID查询时：{"type": 3,"apply_id": 1231,"begin": 0,"count": 3}
     *返回值：{"data": {"devices": [{"comment": "","device_id": 10097,
     * "major": 10001,"minor": 12102,"page_ids": "15369","status": 1,
     * "poi_id": 0,"uuid": "FDA50693-A4E2-4FB1-AFCF-C6EB07647825"},],
     * "total_count": 151},"errcode": 0,"errmsg": "success."}
     */
    public function searchDevice($data)
    {
        $valiRuleArr['type'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        if ($data['type'] === 1) {
            $deviceIdentifiersValiRuleArr = array();
            $valiRuleArr['device_identifiers'] =
                array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
                , ParamValidate::VALI_RULE_ARR => &$deviceIdentifiersValiRuleArr,
                    ParamValidate::MAX_ARR_LEN_VAR => 50);
            foreach ($data['device_identifiers'] as $key => $device_identifier) {
                $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
                    array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
                $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
                $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
                $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
                $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
                $deviceIdentifiersValiRuleArr[$key] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
                , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
            }
        } else {
            $valiRuleArr['begin'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $valiRuleArr['count'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR, ParamValidate::MAX_INT => 50);
            if ($data['type'] === 3) {
                $valiRuleArr['apply_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            }
        }
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_SEARCH, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [bindLocationShakeAroundDevice 修改设备关联的门店ID、设备的备注信息。
     * 可用设备ID 或完整的UUID、Major、Minor指定设备，二者选其一。]
     *
     * @param int $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param int $poi_id 待关联的门店ID
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     */
    public function bindLocationDevice($poi_id, $device_id = 0, $uuid = '', $major = 0, $minor = 0)
    {
        $valiRuleArr['poi_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $device_identifier = self::getDeviceIdentifier($device_id, $uuid, $major, $minor);
        $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
            array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
        $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
        $valiRuleArr['device_identifier'] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
        , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        $data = array(
            'device_identifier' => $device_identifier,
            'poi_id' => $poi_id
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_BINDLOCATION, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [bindPageShakeAroundDevice 配置设备与页面的关联关系。
     * 支持建立或解除关联关系，也支持新增页面或覆盖页面等操作。
     * 配置完成后，在此设备的信号范围内，即可摇出关联的页面信息。
     * 若设备配置多个页面，则随机出现页面信息。]
     *
     * @param int $device_id 设备编号，若填了UUID、major、minor，则可不填设备编号，若二者都填，则以设备编号为优先
     * @param array $page_id_att 待关联的页面列表
     * @param string $uuid UUID、major、minor，三个信息需填写完整，若填了设备编号，则可不填此信息
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     */
    public function  bindPageDevice($page_id_att = array(), $device_id = 0, $uuid = '', $major = 0, $minor = 0)
    {
        $pageIdsValiRuleArr = array();
        foreach ($page_id_att as $key => $pageId) {
            $pageIdsValiRuleArr[$key] =
                array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        }
        $valiRuleArr['page_ids'] =
            array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::MAX_ARR_LEN_VAR => 30
            , ParamValidate::VALI_RULE_ARR => $pageIdsValiRuleArr);
        $device_identifier = self::getDeviceIdentifier($device_id, $uuid, $major, $minor);
        $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
            array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
        $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
        $valiRuleArr['device_identifier'] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
        , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        $data = array(
            'device_identifier' => $device_identifier,
            'page_ids' => $page_id_att
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_BINDPAGE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 查询设备与页面的关联关系
     * @param $data array 查询的条件
     * 当查询指定设备所关联的页面时：
     * {"type": 1,"device_identifier": {
     * "device_id": 10011,"uuid": "###",
     * "major": 1002,"minor": 1223}}
     * 当查询页面所关联的设备时：
     * {"type": 2,"page_id": 11101,"begin": 0,"count": 3}
     * @return mixed
    {"data": {"relations": [
     * {"device_id": 797994,"major": 10001,"minor": 10023,
     * "page_id": 50054,"uuid": "###"
     * },.,.,.],"total_count": 2},
     * "errcode": 0,"errmsg": "success."}
     */
    public function searchRelation($data)
    {
        $valiRuleArr['type'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        if ($data['type'] === 1) {
            $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
                array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
            $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
            $valiRuleArr['device_identifier'] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        } else {
            $valiRuleArr['begin'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $valiRuleArr['count'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $valiRuleArr['page_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        }
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_SEARCH_RELATION, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [addShakeAroundPage 增加摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。]
     * @param $data array 申请页面需要提交的数据
     * @return boolean|mixed
     */
    public function addPage($data)
    {
        /*     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param string $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填*/
        /*$data = array(
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );*/
        $valiRuleArr['title'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 12);
        $valiRuleArr['description'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 14);
        $valiRuleArr['comment'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 30);
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_PAGE_ADD, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [updateShakeAroundPage 编辑摇一摇出来的页面信息，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。]
     * @param array $data 页面信息数组
     * @return boolean|mixed
     */
    public function updatePage($data)
    {
        /*
         * * @param int $page_id
     * @param string $title 在摇一摇页面展示的主标题，不超过6 个字
     * @param string $description 在摇一摇页面展示的副标题，不超过7 个字
     * @param string $icon_url 在摇一摇页面展示的图片， 格式限定为：jpg,jpeg,png,gif; 建议120*120 ， 限制不超过200*200
     * @param string $page_url 跳转链接
     * @param string $comment 页面的备注信息，不超过15 个字,可不填
         * $data = array(
            "page_id" => $page_id,
            "title" => $title,
            "description" => $description,
            "icon_url" => $icon_url,
            "page_url" => $page_url,
            "comment" => $comment
        );*/
        $valiRuleArr['page_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['title'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 12);
        $valiRuleArr['description'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 14);
        $valiRuleArr['comment'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR, ParamValidate::MAX_LEN => 30);
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_PAGE_UPDATE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [searchShakeAroundPage 查询已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * 提供两种查询方式，①可指定页面ID 查询，②也可批量拉取页面列表。]
     *
     * @param array $pageSearchInfo 包含请求的数据。两种方式，一种是指定pageId，另一种指定查询起始数量
     * @return boolean|mixed
     */
    public function searchPage($pageSearchInfo)
    {
        /*if (isset($pageSearchInfo['page_ids'])) {
            $data = array(
                'page_ids' => $pageSearchInfo['page_ids']
            );
        } else {
            $data = array(
                'begin' => $pageSearchInfo['begin'],
                'count' => $pageSearchInfo['count']
            );
        }*/
        $valiRuleArr['type'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        if ($pageSearchInfo['type'] === 1) {
            $pageIdsValiRuleArr = array();
            foreach ($pageSearchInfo['page_ids'] as $key => $pageId) {
                $pageIdsValiRuleArr[$key] =
                    array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            }
            $valiRuleArr['page_ids'] =
                array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
                , ParamValidate::MAX_ARR_LEN_VAR => 50
                , ParamValidate::VALI_RULE_ARR => $pageIdsValiRuleArr);
        } else {
            $valiRuleArr['begin'] =
                array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $valiRuleArr['count'] =
                array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR
                , ParamValidate::MAX_INT => 50);
        }
        $valiState = ParamValidate::validateFuncParam($pageSearchInfo, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_PAGE_SEARCH, $pageSearchInfo);
            return $returnContent;
        }
        return false;
    }

    /**
     * [deleteShakeAroundPage 删除已有的页面，包括在摇一摇页面出现的主标题、副标题、图片和点击进去的超链接。
     * 只有页面与设备没有关联关系时，才可被删除。]
     * @param int $page_id
     * @return boolean|mixed
     */
    public function deletePage($page_id)
    {
        $valiRuleArr['page_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $data = array(
            'page_id' => $page_id
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_PAGE_DELETE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * [getUserShakeInfo 获取设备信息，包括UUID、major、minor，以及距离、openID 等信息。]
     *
     * @param string $ticket 摇周边业务的ticket，可在摇到的URL 中得到，ticket生效时间为30 分钟
     * @return boolean|mixed
     */
    public function getUserShakeInfo($ticket)
    {
        $data = array('ticket' => $ticket);
        $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_USER_GETSHAKEINFO, $data);
        return $returnContent;
    }

    /**
     * [deviceShakeAroundStatistics description，以设备为维度的数据统计接口。]
     *
     * @param int $device_id 设备编号，若填了UUID、major、minor，即可不填设备编号，二者选其一
     * @param int $begin_date 起始日期时间戳，最长时间跨度为30 天
     * @param int $end_date 结束日期时间戳，最长时间跨度为30 天
     * @param string $uuid UUID、major、minor，三个信息需填写完成，若填了设备编号，即可不填此信息，二者选其一
     * @param int $major
     * @param int $minor
     * @return boolean|mixed
     */
    public function statisticsByDevice($begin_date, $end_date, $device_id = 0, $uuid = '', $major = 0, $minor = 0)
    {
        $device_identifier = self::getDeviceIdentifier($device_id, $uuid, $major, $minor);
        $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
            array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
        $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
        $valiRuleArr['device_identifier'] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
        , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        $valiRuleArr['begin_date'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['end_date'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $data = array(
            'device_identifier' => $device_identifier,
            'begin_date' => $begin_date,
            'end_date' => $end_date
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_STATISTICS_DEVICE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 以设备为维度的数据统计接口.
     * @param $page_id string 摇一摇页面id
     * @param int $begin_date 起始日期时间戳，最长时间跨度为30 天
     * @param int $end_date 结束日期时间戳，最长时间跨度为30 天
     * @return bool|mixed
     */
    public
    function statisticsByPage($page_id, $begin_date, $end_date)
    {
        $valiRuleArr['begin_date'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['end_date'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['page_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $data = array(
            'page_id' => $page_id,
            'begin_date' => $begin_date,
            'end_date' => $end_date
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_STATISTICS_PAGE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 上传页面缩略图，并获得图片在微信服务器中的地址
     * @param $fileInfo string 页面缩略图文件名（包含地址）
     * @return bool|string 成功返回图片地址，失败返回false
     */
    public
    function uploadPageIconToWX($fileInfo)
    {
        $varname = 'media';//上传到$_FILES数组中的 key
        $type = $fileInfo['type'];
        $name = $fileInfo['name'];
        $key = "$varname\"; filename=\"$name\r\nContent-Type: $type\r\n";
        ///对上传的图像做一定处理
        $image = new \Think\Image();
        $image->open($fileInfo['tmp_name']);
        $width = $image->width();
        $height = $image->height();
        if ($width != $height || $width > 200 || $height > 200) {
            // 生成一个固定大小为120*120的缩略图并保存为thumb.jpg
            $image->thumb(120, 120, \Think\Image::IMAGE_THUMB_FIXED)->save($fileInfo['tmp_name']);
        }
        ///
        $picContent = file_get_contents($fileInfo['tmp_name']);
        if ($picContent != false) {
            $fields[$key] = $picContent;
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_MATERIAL_ADD, $fields, true);
            $logo_url = $returnContent['data']['pic_url'];
            if ($logo_url) {
                return $logo_url;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 添加设备分组
     * 新建设备分组，每个帐号下最多只有100 个分组。
     * @param $group_name string 设备分组名称
     * @return bool|mixed 成功返回group_id及group_name，失败返回false
     */
    public function addDeviceGroup($group_name)
    {
        $valiRuleArr['group_name'] = array(
            ParamValidate::TYPE => ParamValidate::STRING_VAR
        , ParamValidate::MAX_LEN => 100);
        $data = array(
            'group_name' => $group_name
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_ADD, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 编辑设备分组信息，目前只能修改分组名。
     * @param $group_id int 分组的ID
     * @param $group_name string  新的分组名称
     * @return bool|mixed 成功返回空数组，否则返回false
     */
    public function  updateDeviceGroup($group_id, $group_name)
    {
        $valiRuleArr['group_id'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['group_name'] = array(
            ParamValidate::TYPE => ParamValidate::STRING_VAR
        , ParamValidate::MAX_LEN => 100);
        $data = array(
            'group_id' => $group_id,
            'group_name' => $group_name
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_UPDATE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 删除设备分组
     * 删除设备分组，若分组中迓存在设备，则丌能删除成功。需把设备移除以后，才能删除。
     * @param $group_id
     * @return bool|mixed
     */
    public function deleteDeviceGroup($group_id)
    {
        $valiRuleArr['group_id'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $data = array(
            'group_id' => $group_id
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_DELETE, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 查询账号下所有的分组。
     * @param $begin int
     * @param int $count
     * @return bool|mixed 成功返回分组列表，失败返回false
     */
    public function getGroupList($begin, $count = 1000)
    {
        /*成功返回值：
        "data": {"groups":[
        {"group_id" : 123,"group_name" : "test1"},
        {"group_id" : 124,"group_name" : "test2"}]
        }*/
        $valiRuleArr['begin'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $valiRuleArr['count'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR
        , ParamValidate::MAX_INT => 1000);
        $data['begin'] = $begin;
        $data['count'] = $count;
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_GET_LIST, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 查询分组详情，包括分组名，分组id，分组里的设备列表。
     * @param $group_id int 分组ID
     * @return bool|mixed 成功返回分组详情，失败返回false
     */
    public function  getGroupDetail($group_id)
    {
        /*
         * "data": {"group_id" : 123,"group_name" : "test",
         * "devices" :[
         * {"device_id" : 123456,"uuid" : "",
            "major" : 10001,"minor" : 10001,"comment" : "test device1","poi_id" : 12345,},
           {"device_id" : 123457,"uuid" : "",
            "major" : 10001,"minor" : 10002,"comment" : "test device2","poi_id" : 12345,}]}*/
        $valiRuleArr['group_id'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $data = array(
            'group_id' => $group_id
        );
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_GET_DETAIL, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 添加设备到分组，每个分组能够持有的设备上限为10000，而且每次添加操作的添加
     * 上限为1000。只有在摇周边申请的设备才能添加到分组。
     * @param $data
     * @return bool|mixed
     */
    public function addGroupDevice($data)
    {
        /*提交数据格式
         * {"group_id": 123,
        "device_identifiers":[
        {"device_id":10100,"uuid":"***","major":10001,"minor":10002}]}*/
        /*返回的数据：
        "data": {}*/
        $valiRuleArr['group_id'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifiersValiRuleArr = array();
        $valiRuleArr['device_identifiers'] =
            array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::VALI_RULE_ARR => &$deviceIdentifiersValiRuleArr,
                ParamValidate::MAX_ARR_LEN_VAR => 1000);
        foreach ($data['device_identifiers'] as $key => $device_identifier) {
            $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
                array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
            $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
            $deviceIdentifiersValiRuleArr[$key] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        }
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_DEVICE_ADD, $data);
            return $returnContent;
        }
        return false;
    }

    /**
     * 从分组中移除设备，每次删除操作的上限为1000。
     * @param $data
     * @return bool|mixed
     */
    public function deleteGroupDevice($data)
    {
        /*提交数据格式
         * {"group_id": 123,
        "device_identifiers":[
        {"device_id":10100,"uuid":"***","major":10001,"minor":10002}]}*/
        /*返回的数据：
        "data": {}*/
        errorLog(json_encode($data));
        $valiRuleArr['group_id'] = array(
            ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
        $deviceIdentifiersValiRuleArr = array();
        $valiRuleArr['device_identifiers'] =
            array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::VALI_RULE_ARR => &$deviceIdentifiersValiRuleArr,
                ParamValidate::MAX_ARR_LEN_VAR => 1000);
        foreach ($data['device_identifiers'] as $key => $device_identifier) {
            $deviceIdentifierValiRuleArr[ParamValidate::LOGIC_OR_VAR][] =
                array(ParamValidate::LOGIC_OR_MULTI_VAR => 2, 'device_id', array('major', 'minor', 'uuid'));
            $deviceIdentifierValiRuleArr['device_id'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['major'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['minor'] = array(ParamValidate::TYPE => ParamValidate::INTEGER_VAR);
            $deviceIdentifierValiRuleArr['uuid'] = array(ParamValidate::TYPE => ParamValidate::STRING_VAR);
            $deviceIdentifiersValiRuleArr[$key] = array(ParamValidate::TYPE => ParamValidate::ARRAY_VAR
            , ParamValidate::VALI_RULE_ARR => $deviceIdentifierValiRuleArr);
        }
        $valiState = ParamValidate::validateFuncParam($data, $valiRuleArr);
        if ($valiState) {
            $returnContent = $this->invokeShakeInterface(self::SHAKEAROUND_DEVICE_GROUP_DEVICE_DELETE, $data);
            return $returnContent;
        }
        return false;
    }
}
