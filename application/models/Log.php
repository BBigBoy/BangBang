<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-3-4
 * Time: 下午3:05
 */
class LogModel
{
    private $tableName = 'think_interface_error';

    function addLog($logInfo)
    {
        $logDb = new Db_Mysql();
        $logDb->insert($this->tableName, $logInfo);
    }

}