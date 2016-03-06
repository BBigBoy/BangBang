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

    function findAccount($whereAccount, $fields = '*')
    {
        return $this->accountDb->get_row($this->tableName, $whereAccount, $fields);
    }

    function findMultiAccount($whereAccount, $fields = '*')
    {
        return $this->accountDb->get_all($this->tableName, $whereAccount, $fields);
    }


    function updateAccount($updateInfo, $whereAccount)
    {
        return $this->accountDb->update($this->tableName, $updateInfo, $whereAccount);
    }

    function addAccount($accountInfo)
    {
        return $this->accountDb->update($this->tableName, $accountInfo);
    }

}