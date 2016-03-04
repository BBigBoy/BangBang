<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-3-4
 * Time: 上午11:11
 */
class Bang_UserModel
{
    private $tableName = 'think_bangbang_user';

    function findUser($findInfo)
    {
        $taskDb = new Db_Mysql();
        return $taskDb->get_row($this->tableName, $findInfo);
    }

    function addUser($userInfo)
    {
        $taskDb = new Db_Mysql();
        return $taskDb->insert($this->tableName, $userInfo);
    }


}