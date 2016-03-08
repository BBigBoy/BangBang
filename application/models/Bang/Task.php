<?php

class Bang_TaskModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_bangbang_task';
    protected $primaryKey = '_id';

    /**
     * @return array
     */
    public function getTasks()
    {
        $select = 'think_fans_info.headimgurl,think_bangbang_task.instruction,think_bangbang_task.category';
        $taskAtt = $this->selectRaw($select)
            ->leftJoin('think_fans_info', 'think_bangbang_task.publish_user_openid', '=', 'think_fans_info.openid')
            ->limit(20)
            ->orderBy('think_bangbang_task._id', 'desc')
            ->get();
        return $taskAtt;
    }

    function findTask($findInfo)
    {
        return $this->where($findInfo)->first();
    }

    function addTask($taskInfo)
    {
        return $this->insert($taskInfo);
    }

}