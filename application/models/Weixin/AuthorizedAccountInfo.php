<?php

class Weixin_AuthorizedAccountInfoModel
{

    private $tableName = 'think_authorized_account_info';
    private $accountDb;

    public function __construct()
    {
        $this->accountDb = new Db_Mysql();
    }

    function findAccount($whereAuthorizer)
    {
        return $this->accountDb->get_row($this->tableName, $whereAuthorizer);
    }

    public function addUser($userInfo)
    {
        return $this->accountDb->insert($this->tableName, $userInfo);
    }
}