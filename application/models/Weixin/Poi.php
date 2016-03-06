<?php

class Weixin_PoiModel
{
    private $tableName = "think_%s_poi";
    private $poiDb;

    /**
     * Weixin_JSTicketModel constructor.
     */
    public function __construct($auth_app_id)
    {
        sprintf($this->tableName, $auth_app_id);
        $this->poiDb = new Db_Mysql();
    }

    public function delAll()
    {
        return $this->poiDb->delete($this->tableName, array(1 => 1));
    }

    public function addAll($poiList)
    {///只是通過循環實現了批量導入
        $addtate = true;
        foreach ($poiList as $poi) {
            $addtate=  $this->poiDb->insert($this->tableName, $poi);
            if(!$addtate){
                return $addtate;
            }
        }
        return $addtate;
    }

    public function findMultiPoi($wherePoi, $fields = '*')
    {
        return $this->poiDb->get_all($this->tableName, $wherePoi, $fields);
    }
}