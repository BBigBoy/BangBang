<?php

class Bang_CommentModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_bangbang_comment';
    protected $primaryKey = '_id';

    function insertComment($commentInfo)
    {
        return $this->insertGetId($commentInfo);
    }

    function getComments($taskId)
    {
        $whereCondition['task'] = $taskId;
        $select = 'nickname,headimgurl,think_bangbang_comment._id,content,post_time,aim_comment';
        return $this
            ->where($whereCondition)
            ->selectRaw($select)
            ->leftJoin('think_bangbang_user', 'think_bangbang_comment.user', '=', 'think_bangbang_user._id')
            ->orderBy('think_bangbang_comment._id', 'asc')
            ->get();
    }

}