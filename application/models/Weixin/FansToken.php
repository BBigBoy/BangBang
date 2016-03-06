<?php

class Weixin_AccountAuthInfoModel
{
    private $tableName = 'think_account_authorizer_info';
    private $accountDb;


    /**
     * Weixin_AccountAuthInfoModel constructor.
     */
    public function __construct()
    {
        $this->accountDb = new Db_Mysql();
    }

    function findAccount($whereAccount)
    {
        return $this->accountDb->get_row($this->tableName, $whereAccount);
    }

    function updateAccount($updateInfo, $whereAccount)
    {
        return $this->accountDb->update($this->tableName, $updateInfo, $whereAccount);
    }

}