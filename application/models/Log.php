<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-3-4
 * Time: 下午3:05
 */
class LogModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_interface_error';

    function addLog($logInfo)
    {
        return $this->insert($logInfo);
    }

}