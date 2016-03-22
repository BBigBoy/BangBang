<?php

class Weixin_FansInfoModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_fans_info';


    function findFans($whereFans, $fields = '*')
    {
        return $this::where($whereFans)->select(is_array($fields) ? $fields : explode(',', $fields))->first();
    }

    function findMultiFans($whereFans, $fields = '*')
    {
        return $this::where($whereFans)->select(explode(',', $fields))->get();
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