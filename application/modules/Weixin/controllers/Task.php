<?php
/**
 * Created by PhpStorm.
 * User: BigBigBoy
 * Date: 2015/7/18
 * Time: 22:54
 */

namespace Platform\Controller;

use Common\Common\AsyncTaskManager;
use Think\Controller;

class TaskQueueController extends Controller
{

    /**
     *秒级异步任务。当前频率为5s/次
     * 本方法将会被自动调用，取出任务，并处理
     * @param int $taskId
     * @param string $className
     * @param string $methodName
     */
    public function secondsTask($taskId = 0, $className = '', $methodName = '')
    {
        $echoStr = '';
        $asyncTaskInfo = AsyncTaskManager::getAsyncTask($taskId, $className, $methodName);
        if ($asyncTaskInfo) {
            $dealResult = AsyncTaskManager::dealAsyncTask($asyncTaskInfo);
            if ($dealResult === false) {
                errorLog('异步任务处理失败：' . json_encode($asyncTaskInfo));
                $echoStr = ' secondsTask  execute fail! ---> taskID:' . $asyncTaskInfo['id'] . "\n   --->taskInfo:" . json_encode($asyncTaskInfo);
            } else {
                $echoStr = ' secondsTask  execute success! ---> taskID:' . $asyncTaskInfo['id'] . "\n   --->taskInfo:" . json_encode($asyncTaskInfo);
            }
        }
        echo "\n\n" . time() . "\n" . ($echoStr ?: ' There is no task!');
    }

    /**
     *日级任务，每天都需要执行的任务
     * 当前设定于每天凌晨2点执行.
     * 如果任务量不大，可以直接在这里面执行；
     * 任务量大，可以添加到任务队列中，由秒级任务处理，本方法仅仅作为秒极任务调度。
     */
    public function dailyTask()
    {
        AsyncTaskManager::addAsyncTask('Platform\Common\WXDailyTask',
            'remainEffectiveAuthorizerRefreshToken', array(), 20, 5, '同步授权微信公众帐号授权刷新口令', array());
        echo "\n\n" . time() . "\ndailyTask";
    }


    public function test()
    {
        while (1) ;
        return true;
    }


    public function test2()
    {
        echo S('RunningAsyncTasks');
        echo S('RunningAsyncTasks', null);
    }

    public function test3()
    {
        echo phpinfo();
    }

    public function teshhh()
    {

    }

    public function testa()
    {
        echo "start<br>";
        $_GET['taskId'] = 1422;

        $i = 1;
        while ($i++) {
            if ($i % 1000000 === 0)
                echo "testa\n";
        }
        return true;
    }

}