<?php
/**
 * @name IndexModel
 * @desc index数据获取类, 可以访问数据库，文件，其它系统等
 * @author root
 */
class Home_IndexModel extends \Illuminate\Database\Eloquent\Model {

    public function selectSample() {
        return 'Hello Home_IndexModel extends \Illuminate\Database\Eloquent\Model!';
    }

    public function insertSample($arrInfo) {
        return true;
    }
}
