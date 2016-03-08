<?php

class Weixin_AccountAuthInfoModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_account_authorizer_info';

    function delAccount($whereAccount)
    {
        return $this::where($whereAccount)->delete();
    }

    function findAccount($whereAccount, $fields = '*')
    {
        return $this::where($whereAccount)->select($fields)->first();
    }

    function findMultiAccount($whereAccount, $fields = '*')
    {
        return $this::where($whereAccount)->select($fields)->get();
    }


    function updateAccount($whereAccount, $updateInfo)
    {
        return $this::update($whereAccount, $updateInfo);
    }

    function addAccount($accountInfo)
    {
        return $this::insert($accountInfo);
    }

}