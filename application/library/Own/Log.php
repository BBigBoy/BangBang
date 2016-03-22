<?php

/*（1）DEBUG (100): Detailed debug information.
（2）INFO (200): Interesting events. Examples: User logs in, SQL logs.
（3）NOTICE (250): Normal but significant events.
（4）WARNING (300): Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
（5）ERROR (400): Runtime errors that do not require immediate action but should typically be logged and monitored.
（6）CRITICAL (500): Critical conditions. Example: Application component unavailable, unexpected exception.
（7）ALERT (550): Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
（8）EMERGENCY (600): Emergency: system is unusable.*/

class Own_Log extends \Monolog\Logger
{
    private $channelName = 'bigbigboy';
    /** Singleton instance */
    private static $_instance = null;

    public function __construct()
    {
        $pdo = \Illuminate\Database\Capsule\Manager::schema()->getConnection()->getPdo();
        $mySQLHandler = new MySQLHandler\MySQLHandler($pdo, "log", array(), \Monolog\Logger::INFO);
        parent::__construct($this->channelName, array($mySQLHandler));
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}