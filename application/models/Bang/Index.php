<?php

/**
 * @name IndexModel
 * @desc index数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class Bang_IndexModel
{
    /*   public $tableSql = <<<DB
   CREATE TABLE IF NOT EXISTS `bigbigboy` (
     `device_id` int(11) NOT NULL,
     `major` int(11) NOT NULL,
     `minor` int(11) NOT NULL,
     `device_sn` char(30) DEFAULT NULL,
     `visit_num` int(11) DEFAULT '0',
     `live_num` int(11) DEFAULT '0',
     PRIMARY KEY (`device_id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
   DB;*/
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

    function findUser($findInfo)
    {
        $taskDb = new Db_Mysql();
        return $taskDb->get_row('think_bangbang_user', $findInfo);
    }

    function addUser($userInfo)
    {
        $taskDb = new Db_Mysql();
        return $taskDb->insert('think_bangbang_user', $userInfo);
    }


}