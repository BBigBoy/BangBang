# BangBang
基于YAF框架的PHP WEB 应用;

兼容本地环境和新浪云,分别做了兼容配置;

模版框架使用的是:Smarty,由于新浪云的IO限制,本地和新浪云使用了不同的版本,但都是Smarty3.0;

数据库ORM使用的是Eloquent(强大便捷);

日志系统目前使用的是Monolog,然后使用mysql存储;(主要是新浪云的环境限制,如果是自己的主机,可以考虑SeasLog);

项目中使用到的两个超级缓存函数 S 和 F函数,来自于ThinkPHP框架,本地都是采用File的方式,新浪云中,S函数用到了Memcache,F函数使用的是KVDB;