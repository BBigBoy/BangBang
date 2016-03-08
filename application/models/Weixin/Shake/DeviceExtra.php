<?php

class Weixin_Shake_DeviceExtraModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_device_extra";

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
                    $table->integer('device_id');
                    $table->primary('device_id');
                    $table->integer('major');
                    $table->integer('minor');
                    $table->char('device_sn', 30)->nullable()->default(null);
                    $table->integer('visit_num')->default(0);
                    $table->integer('live_num')->default(0);
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }

    function incLiveNum($which)
    {
        return $this::where($which)->increment('live_num');
    }

    function updateDeviceExtra($whereDevice,$updateInfo){
        return $this::update($whereDevice,$updateInfo);
    }
}