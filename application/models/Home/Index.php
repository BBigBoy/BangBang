<?php
/**
 * @name IndexModel
 * @desc index数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class Home_IndexModel {
    public function __construct() {
    }   
    
    public function selectSample() {
        return 'Hello Home_IndexModel!';
    }

    public function insertSample($arrInfo) {
        return true;
    }
}
