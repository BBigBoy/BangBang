<?php
/**
 * @name Bootstrap
 * @author root
 * @desc 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract
{

    public function _initConfig()
    {
        //把配置保存起来
        $arrConfig = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $arrConfig);
    }

    public function _initFunc()
    {
        Yaf_Loader::import(APPLICATION_PATH.'/application/library/function.php');
        Yaf_Loader::import(APPLICATION_PATH.'/application/library/Weixin/WXOpenplatform.php');
    }

    /*public function _initPlugin(Yaf_Dispatcher $dispatcher)
     {
         //注册一个插件
         $objSamplePlugin = new SamplePlugin();
         $dispatcher->registerPlugin($objSamplePlugin);
     }*/

    public function _initLocalName()
    {
        /** we put class Smarty_Adapter under the local library directory */
        Yaf_Loader::getInstance()->registerLocalNamespace(array('Smarty', 'Own'));
    }

    public function _initSmarty(Yaf_Dispatcher $dispatcher)
    {
        $smarty = new Smarty_Adapter(null, Yaf_Registry::get("config")->get("smarty"));
        $dispatcher->setView($smarty);
        /* now the Smarty view engine become the default view engine of Yaf */
    }
    // 初始化 Eloquent ORM
    public function _initDefaultDbAdapter(Yaf_Dispatcher $dispatcher)
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection(Yaf_Registry::get("config")->database->toArray());
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
    public function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        //在这里注册自己的路由协议,默认使用简单路由
    }

    public function _initView(Yaf_Dispatcher $dispatcher)
    {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }
}
