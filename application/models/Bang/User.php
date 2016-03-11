<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-3-4
 * Time: 上午11:11
 */
class Bang_UserModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_bangbang_user';

    function findUser($findInfo)
    {
        return $this::where($findInfo)->first();
    }

    function addUser($userInfo)
    {
        return $this::insert($userInfo);
    }


}