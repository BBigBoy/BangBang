<?php

class Weixin_FansTokenModel
{
    private $tableName = 'think_o_auth_fans_token';
    private $fansDb;


    /**
     * Weixin_AccountAuthInfoModel constructor.
     */
    public function __construct()
    {
        $this->fansDb = new Db_Mysql();
    }

    function findFans($whereFans, $fields = '*')
    {
        return $this->fansDb->get_row($this->tableName, $whereFans, $fields);
    }

    function updateFans($whereFans, $fansInfo)
    {
        return $this->fansDb->update($this->tableName, $fansInfo, $whereFans);
    }

    function addFans($fansInfo)
    {
        return $this->fansDb->insert($this->tableName, $fansInfo);
    }

}