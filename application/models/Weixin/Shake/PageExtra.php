<?php

class Weixin_Shake_PageExtraModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_page_extra";

    /**
     * Weixin_PoiModel constructor.
     * @param array $auth_app_id
     * @param array $attributes
     */
    public function __construct($auth_app_id, array $attributes = [])
    {
        $this->table = sprintf($this->table, $auth_app_id);
        parent::__construct($attributes);
    }

    function createTable()
    {
        if (!\Illuminate\Database\Capsule\Manager::schema()->hasTable($this->table)) {
            \Illuminate\Database\Capsule\Manager::schema()
                ->create($this->table, function (Illuminate\Database\Schema\Blueprint $table) {
                    $table->integer('page_id');
                    $table->primary('page_id');
                    $table->integer('display_num');
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }


    function incDisplayNum($which)
    {
        return $this::whereIn('page_id',$which)->increment('display_num');
    }
}