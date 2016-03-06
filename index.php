<?php
define('APPLICATION_PATH', dirname(__FILE__));
require APPLICATION_PATH.'/vendor/autoload.php';
if (function_exists('saeAutoLoader')) {// 自动识别SAE环境
    define('APP_MODE', 'sae');
}else{
    define('APP_MODE', 'local');
}
$application = new Yaf_Application(APPLICATION_PATH . "/conf/application.ini",APP_MODE);
$application->bootstrap()->run();
