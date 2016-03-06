<?php

class Weixin_FansTokenModel
{
    private $tableName = 'think_o_auth_fans_token';
    private $fansTokenDb;


    /**
     * Weixin_AccountAuthInfoModel constructor.
     */
    public function __construct()
    {
        $this->fansTokenDb = new Db_Mysql();
    }

    function findFans($whereFans, $fields = '*')
    {
        return $this->fansTokenDb->get_row($this->tableName, $whereFans, $fields);
    }

    function updateFans($whereFans, $fansInfo)
    {
        return $this->fansTokenDb->update($this->tableName, $fansInfo, $whereFans);
    }

    function addFans($fansInfo)
    {
        return $this->fansTokenDb->insert($this->tableName, $fansInfo);
    }

}