<?php

class Weixin_Shake_DeviceApplyModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_device_apply";

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
                    $table->integer('id', true);
                    $table->integer('handle_state')->default(0);
                    $table->integer('device_num');
                    $table->integer('poi_id')->default(0);
                    $table->char('comment', 30)->nullable()->default(null);
                    $table->bigInteger('apply_time');
                    $table->integer('apply_id');
                    $table->integer('audit_status');
                    $table->integer('audit_comment');
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }
}