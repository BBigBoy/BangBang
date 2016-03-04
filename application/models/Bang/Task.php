<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-3-4
 * Time: 上午11:14
 */
class Bang_TaskModel
{
    /**
     * @return array
     */
    public function getTasks()
    {
        $taskDb = new Db_Mysql();
        $taskAtt = $taskDb
            ->run("select think_fans_info.headimgurl,think_bangbang_task.instruction,think_bangbang_task.category
               from think_bangbang_task
               left join think_fans_info
               ON think_bangbang_task.publish_user_openid = think_fans_info.openid
               order by think_bangbang_task._id desc limit 20;")
            ->fetchAll();
        return $taskAtt;
    }

    function findTask($findInfo)
    {
        $taskDb = new Db_Mysql();
        return $taskDb->get_row('think_bangbang_task', $findInfo);
    }
    function addTask($taskInfo){
        $taskDb = new Db_Mysql();
        return $taskDb->insert('think_bangbang_task', $taskInfo);
    }

}