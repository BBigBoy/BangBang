<?php

class Weixin_AuthorizedAccountInfoModel extends \Illuminate\Database\Eloquent\Model
{

    public $timestamps = false;
    protected $table = 'think_authorized_account_info';


    function findAccount($whereAuthorizer)
    {
        return $this::where($whereAuthorizer)->first();
    }

    public function addUser($userInfo)
    {
        return $this::insert($userInfo);
    }
}