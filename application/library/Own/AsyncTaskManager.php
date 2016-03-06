<?php
namespace Common\Common;

interface AsyncTask
{
    public function execute();
}

/**
 * 异步任务处理类
 * 1、通过addAsyncTask添加一个异步任务
 * 2、通过getAsyncTask获得一个待处理的异步任务信息
 * 3、通过dealAsyncTask处理一个异步任务
 * 4、通过updateAsyncTaskState更新异步任务的处理状态
 * WARNING：要求添加后台任务的方法必须为bool类型的返回值。
 * 必须为true或者false，处理结果将使用“全等”即“===”判断，
 * 因此应该保证为bool类型的返回值，不应该是其他类型的值。
 * Created by PhpStorm.
 * User: BigBigBoy
 * Date: 2015/10/25
 * Time: 11:29
 */
class AsyncTaskManager
{

    /**
     *限制同时执行的最大任务数
     */
    const MaxAsyncTask = 10;

    /**
     * 添加异步任务
     * @param $className string 原始类名（含包名） 一般通过PHP常量__CLASS__即可获取
     * @param $methodName string 需要回调的方法名  通过PHP常量__METHOD__即可获取
     * @param $parameterAtt array 如果异步任务需要回掉的方法必须传参，则从此传入。
     *                             内容为键值对，键为参数名称。
     * @param int $repeat 当前任务如果执行不成功所需要重复执行的次数，默认为0，代表不要重复
     * @param $priority int 设定此异步任务的优先级别。
     * 取值范围为0~255.
     * 默认为0，优先级为最低。
     * 建议后台维护任务不要修改优先级。用户请求任务的优先级设置为255.
     * @param $comment string 任务备注
     * @param array $classConstructParameter 类的构造函数需要传的参数
     * @return bool|mixed 添加成功返回true，失败返回false
     */
    public static function addAsyncTask($className, $methodName, $parameterAtt = array()
		, $repeat = 0, $priority = 0, $comment = '', $classConstructParameter = array())
    {
        if (!($className && is_string($className))) {
            errorLog('$className错误');
            return false;
        } elseif (!($methodName && is_string($methodName))) {
            errorLog('$methodName错误');
            return false;
        } elseif (!(is_int($priority))) {
            errorLog('$priority错误');
            return false;
        } elseif (!(is_array($parameterAtt))) {
            errorLog('$parameterAtt错误');
            return false;
        } elseif (!(is_string($comment))) {
            errorLog('$parameterAtt错误');
            return false;
        } elseif (!(is_array($classConstructParameter))) {
            errorLog('$classConstructParameter错误');
            return false;
        }
        $asyncTask['class'] = $className;
        $asyncTask['method'] = $methodName;
        $asyncTask['priority'] = $priority;
        $asyncTask['parameter'] = json_encode($parameterAtt, JSON_UNESCAPED_UNICODE);
        $asyncTask['status'] = 0;
        $asyncTask['repeat'] = $repeat;
        $asyncTask['comment'] = $comment;
        $asyncTask['add_time'] = time();
        $asyncTask['class_construct_parameter'] = json_encode($classConstructParameter);
        $debugBacktrace = debug_backtrace();
        $invokeMethodInfo = $debugBacktrace[1];
        if ($invokeMethodInfo['class'] === $className && $invokeMethodInfo['function'] === $methodName) {
            errorLog('任务添加失败(不允许方法将自己加入异步任务中)');
            return false;
        }
        $asyncTaskModel = M('AsyncTask');
        $addResult = $asyncTaskModel->add($asyncTask);
        if (!$addResult) {
            errorLog('任务添加失败');
        } else {
            $asyncUrl = $_SERVER['HTTP_HOST'] . '/index.php/Platform/TaskQueue/secondsTask?cron=*';
            async_http_request_no_result($asyncUrl);
        }
        return $addResult;
    }

    /**
     * 从数据库中取出未处理完成的任务。优先级越高越早取出。
     * TODO:这里面的处理机制应该根据实际使用情况更新，
	 * 目前需要考虑的情况包括对于上次未完成任务的处理。
     * @param $taskId int 任务ID
     * @param string $className 指定属于某一个类的异步任务，默认不指定
     * @param string $methodName 指定属于某一个类的特定方法的异步任务，默认不指定
     * @return mixed
     */
    public static function getAsyncTask($taskId = 0, $className = '', $methodName = '')
    {
        $asyncTaskModel = M('AsyncTask');
        if ($taskId) {
            $where['id'] = $taskId;
            $where['status'] = array('neq', 1);
            $where['repeat'] = array('neq', 0);
        } else {
            if (S('RunningAsyncTasks')) {
                /*$runningTasksStr = str_replace('-', '', trim(S('RunningAsyncTasks'), ','));
                $runningTaskAtt = explode(',', $runningTasksStr);
                $unCompleteTask = '';
                foreach ($runningTaskAtt as $runningTask) {
                    if (S('AsyncTask' . $runningTask) === false) {
                        //进入本条件表明此项任务未按照正常情况执行完成，需要进行标记
                        $unCompleteTask .= ($runningTask . ',');
                        $newRunningAsyncTasks = str_replace('-' . $runningTask . ',', '', S('RunningAsyncTasks'));
                        S('RunningAsyncTasks', $newRunningAsyncTasks ? $newRunningAsyncTasks : null);
                    }
                }
                if ($unCompleteTask) {
                    AsyncTaskManager::updateAsyncTaskState($unCompleteTask, 2);
                }*/
                $newRunningAsyncTasks = str_replace('-', '', trim(S('RunningAsyncTasks'), ','));
                if ($newRunningAsyncTasks) {
                    $where['id'] = array('not in', $newRunningAsyncTasks);
                    if (count(array_unique(explode(',', $newRunningAsyncTasks))) > self::MaxAsyncTask) {
                        return null;
                    }
                }
            }
            if ($className) {
                $where['class'] = $className;
                if ($methodName) {
                    $where['method'] = $methodName;
                }
            }
            $where1['status'] = 0;
            $where1['_logic'] = 'or';
            $where1['_complex'] = array('status' => 2, 'repeat' => array('gt', 0));
            $where['_complex'] = $where1;
        }
        $asyncTaskItem = $asyncTaskModel->order(' priority desc,status desc ')->where($where)->find();
        return $asyncTaskItem;
    }

    /**
     * 更新指定异步任务的执行状态
     * @param $taskIdStr string 指定异步任务ID.可以是单一任务的id，
     * 也可以是'1,2,3',或者array('1','2','3')格式
     * @param int $status 标记异步任务处理状态，默认为1，表示处理成功。
     * 如果传入2时，则系统将在下一次执行任务时，重新处理这次未完成的任务。
     * -1，表示此次处理未成功。
     * -2,表示此异步任务未按照指定标准实现。
     * warning：$status为-128~127之间的整数
     * @return bool 执行状态。成功返回true，失败返回false。
     */
    public static function updateAsyncTaskState($taskIdStr, $status = 1)
    {
        $asyncTaskModel = M('AsyncTask');
        $where['id'] = is_int($taskIdStr) ? $taskIdStr : array('in', $taskIdStr);
        $save['status'] = $status;
        $save['deal_time'] = time();
        $sqlState = $asyncTaskModel->where($where)->save($save);
        return $sqlState;
    }

    /**
     * 处理异步任务
     * @param $aTaskItem array 包含异步任务信息的任务项
     * @return mixed 失败返回false，成功返回对应的任务id
     */
    public static function dealAsyncTask($aTaskItem)
    {
        register_shutdown_function(array('Common\Common\AsyncTaskManager', 'fatalError'));
        if (!($aTaskItem && is_array($aTaskItem))) {
            errorLog(json_encode($aTaskItem) . '<-->$aTaskItem错误');
            return false;
        }
        if (!($aTaskItem['class'] && is_string($aTaskItem['class']))) {
            errorLog(json_encode($aTaskItem) . '<-->未指定异步任务执行类');
            return false;
        }
        if (!($aTaskItem['method'] && is_string($aTaskItem['method']))) {
            errorLog(json_encode($aTaskItem) . '<-->未指定异步任务执行方法');
            return false;
        }
        $methodArgs = json_decode($aTaskItem['parameter'], true);
        if (!(is_array($methodArgs))) {
            errorLog(json_encode($aTaskItem) . '<-->方法传入的参数错误（格式有误）');
            return false;
        }
        $classArgs = json_decode($aTaskItem['class_construct_parameter'], true);
        if (!(is_array($classArgs))) {
            errorLog(json_encode($aTaskItem) . '<-->类构造函数传入的参数错误（格式有误）');
            return false;
        }
        try {
            $class = new \ReflectionClass($aTaskItem['class']);
            $classConstructor = $class->getConstructor();
            $classParams = $classConstructor->getParameters();
            $classConstructorArgs = array();
            foreach ($classParams as $param) {
                $paramName = $param->getName();
                if (isset($classArgs[$paramName])) {
                    $classConstructorArgs[] = $classArgs[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $classConstructorArgs[] = $param->getDefaultValue();
                }
            }
            if (count($classConstructorArgs) == $classConstructor->getNumberOfParameters()) {
                $instance = $class->newInstanceArgs($classConstructorArgs);
            }
            $method = new \ReflectionMethod($aTaskItem['class'], $aTaskItem['method']);
            $params = $method->getParameters();
            $args = array();
            foreach ($params as $param) {
                $paramName = $param->getName();
                if (isset($methodArgs[$paramName])) {
                    $args[] = $methodArgs[$paramName];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                }
            }
            if (count($args) == $method->getNumberOfParameters()) {
                //标记当前任务执行状态，缓存时间为150s。
                //即单个任务最长执行时间限制在150s，具体时间由php运行环境决定
                $indentify = '-' . $aTaskItem['id'] . ',';
                S('RunningAsyncTasks', S('RunningAsyncTasks') . $indentify);
//                S('AsyncTask' . $aTaskItem['id'], time(), 150);
                //通过get参数传递一个当前执行的任务ID，帮助方法区分具体的执行工作，这在需要多次执行才能完成的任务中非常有效
                $_GET['AsyncTaskID'] = $aTaskItem['id'];
                if ($aTaskItem['repeat'] > 0) {
                    $asyncTaskModel = M('AsyncTask');
                    $asyncTaskModel->where(array('id' => $aTaskItem['id']))->save(array('repeat' => array('exp', ' (`repeat`-1) ')));
                }
                ///由于这个任务可能无法一次完成，所以需要在后续任务重判断是否已完成该任务
                $asyncTaskResult = $method->invokeArgs($instance, $args);
                ///
                $newRunningAsyncTasks = str_replace($indentify, '', S('RunningAsyncTasks'));
                //当S函数设置的值为'',即空字符串时，设置不会成功，所以需要判断
                S('RunningAsyncTasks', $newRunningAsyncTasks ?: null);
//                S('AsyncTask' . $aTaskItem['id'], null);
                if ($asyncTaskResult === true) {
                    AsyncTaskManager::updateAsyncTaskState($aTaskItem['id']);
                    return (int)($aTaskItem['id']);
                } elseif ($asyncTaskResult === false) {
                    AsyncTaskManager::updateAsyncTaskState($aTaskItem['id'], -1);
                    return false;
                } else {
                    errorLog('添加的任务返回值未按标准实现，不是bool类型变量！');
                    AsyncTaskManager::updateAsyncTaskState($aTaskItem['id'], -2);
                    return false;
                }
            } else {
                errorLog(json_encode($aTaskItem) . '<-->方法传入的参数错误（参数不全）');
                return false;
            }
        } catch (\ReflectionException $e) {
            errorLog(json_encode($aTaskItem) . '<-->方法传入的参数错误（不存在指定的类或方法）');
            return false;
        }
    }

    /**
     *通过register_shutdown_function注册的当php脚本结束时调用的方法，这里是为了捕捉超时异常
     */
    public static function fatalError()
    {
        $e = error_get_last();
//        errorLog($_GET['AsyncTaskID'] . 'uuuu' . S('RunningAsyncTasks'));
        if (isset($_GET['AsyncTaskID']) && isset($e['message']) && (strpos($e['message'], 'Maximum execution time') !== false)) {
            if (isset($_GET['cron']) && ($_GET['cron'] != '*+')) {
                AsyncTaskManager::updateAsyncTaskState($_GET['AsyncTaskID'], 2);
            }
            $runningAsyncTasks = str_replace('-' . $_GET['AsyncTaskID'] . ',', '', S('RunningAsyncTasks'));
            //当S函数设置的值为'',即空字符串时，设置不会成功，所以需要判断
            S('RunningAsyncTasks', $runningAsyncTasks ?: null);
            $asyncUrl = $_SERVER['SCRIPT_URI'] . '?cron=*+' . '&taskId=' . $_GET['AsyncTaskID'];
            async_http_request_no_result($asyncUrl);
            errorLog($asyncUrl);
        }
    }

}