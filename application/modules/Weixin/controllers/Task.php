<?php

class TaskController extends Own_Controller_Base
{

    /**
     *秒级异步任务。当前频率为5s/次
     * 本方法将会被自动调用，取出任务，并处理
     * @param int $taskId
     * @param string $className
     * @param string $methodName
     */
    public function secondsTaskAction($taskId = 0, $className = '', $methodName = '')
    {
        $echoStr = '';
        $asyncTaskInfo = Own_AsyncTaskManager::getAsyncTask($taskId, $className, $methodName);
        if ($asyncTaskInfo) {
            $dealResult = Own_AsyncTaskManager::dealAsyncTask($asyncTaskInfo);
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
    public function dailyTaskAction()
    {
        Own_AsyncTaskManager::addAsyncTask('Platform\Common\WXDailyTask',
            'remainEffectiveAuthorizerRefreshToken', array(), 20, 5, '同步授权微信公众帐号授权刷新口令', array());
        echo "\n\n" . time() . "\ndailyTask";
    }

}