<?php
/**
 * 公众号账号信息类
 */
class Weixin_Account_Info
{
    private $nick_name;
    private $head_img_url;
    private $service_type_info;
    private $verify_type_info;
    private $user_name;
    private $alias;
    private $qrcode_url;
    private $func_info_list = '';

    /**
     * @return mixed
     */
    public function getNickName()
    {
        return $this->nick_name;
    }

    /**
     * @return mixed
     */
    public function getServiceTypeInfo()
    {
        return $this->service_type_info;
    }

    /**
     * @return mixed
     */
    public function getHeadImgUrl()
    {
        return $this->head_img_url;
    }

    /**
     * @return mixed
     */
    public function getVerifyTypeInfo()
    {
        return $this->verify_type_info;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return mixed
     */
    public function getQrcodeUrl()
    {
        return $this->qrcode_url;
    }

    /**
     * @return string
     */
    public function getFuncInfoList()
    {
        return $this->func_info_list;
    }

    function constructFromAtt($accountInfoAtt)
    {
        $this->nick_name = $accountInfoAtt['nick_name'];
        $this->head_img_url = $accountInfoAtt['head_img_url'];
        $this->service_type_info = $accountInfoAtt['service_type_info'];
        $this->verify_type_info = $accountInfoAtt['verify_type_info'];
        $this->user_name = $accountInfoAtt['user_name'];
        $this->alias = $accountInfoAtt['alias'];
        $this->qrcode_url = $accountInfoAtt['qrcode_url'];
        $this->func_info_list = $accountInfoAtt['funclist'];
    }

    public function constructFromStr($accountInfoStr)
    {
        $account_info_originObj = json_decode($accountInfoStr);
        $authorizer_info = $account_info_originObj->authorizer_info;
        $this->nick_name = ($authorizer_info->nick_name);
        $this->head_img_url = ($authorizer_info->head_img);
        $this->service_type_info = ($authorizer_info->service_type_info->id);
        $this->verify_type_info = ($authorizer_info->verify_type_info->id);
        $this->user_name = ($authorizer_info->user_name);
        $this->alias = ($authorizer_info->alias);
        $this->qrcode_url = ($authorizer_info->qrcode_url);
        $func_info = $account_info_originObj->authorization_info->func_info;
        $this->func_info_list = '';
        foreach ($func_info as $i => $value) {
            $this->func_info_list .= ($value->funcscope_category->id . ';');
        }
    }

}