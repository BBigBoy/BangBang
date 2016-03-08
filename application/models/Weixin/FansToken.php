<?php

class Weixin_FansTokenModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_o_auth_fans_token';


    function findFans($whereFans, $fields = '*')
    {
        return $this::where($whereFans)->select($fields)->first();
    }

    function updateFans($whereFans, $fansInfo)
    {
        return $this::update($whereFans, $fansInfo);
    }

    function addFans($fansInfo)
    {
        return $this::insert($fansInfo);
    }

}