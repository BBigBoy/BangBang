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
        $select = 'nickname,headimgurl,instruction,category,status,reward,time_start,think_bangbang_task._id';
        return $this
            ->selectRaw($select)
            ->leftJoin('think_bangbang_user', 'think_bangbang_task.publish_user', '=', 'think_bangbang_user._id')
            ->limit(20)
            ->orderBy('think_bangbang_task._id', 'desc')
            ->get();
    }

    function findTask($taskId)
    {
        $select = 'nickname,headimgurl,instruction,category,status,reward,time_start,time_end,think_bangbang_task._id';
        return $this
            ->selectRaw($select)
            ->where(array('think_bangbang_task._id' => $taskId))
            ->leftJoin('think_bangbang_user', 'think_bangbang_task.publish_user', '=', 'think_bangbang_user._id')
            ->first();
    }

    function addTask($taskInfo)
    {
        return $this->insert($taskInfo);
    }

}