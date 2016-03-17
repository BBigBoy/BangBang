<?php

class Weixin_Shake_DeviceModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_device";

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
                    $table->integer('poi_id');
                    $table->integer('group_id')->default(0);
                    $table->char('status', 1);
                    $table->string('uuid', 36);
                    $table->string('comment', 30)->nullable()->default(null);
                    $table->longText('page_ids')->nullable()->default(null);
                    $table->bigInteger('last_active_time');
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }

    function findDevice($whereDevice, $fields = '*')
    {
        return $this::where($whereDevice)->select(explode(',', $fields))->first();
    }

    function updateDevice($whereDevice, $deviceInfo)
    {
        return $this::update($whereDevice, $deviceInfo);
    }

    function updateDeviceInList($deviceList, $deviceInfo)
    {
        return $this::whereIn('device_id', $deviceList)->update($deviceInfo);
    }

    public function delAll()
    {
        $this->truncate();
    }

    public function countNum()
    {
        return $this->count();
    }

    public function addAll($shakeDeviceAtt)
    {
        return $this->insert($shakeDeviceAtt);
    }

    function selectNullPageDevices()
    {
        return $this->whereNull('page_ids')->select('device_id')->get();
    }

    function selectDeviceNotInList($deviceAtt, $fields = '*')
    {
        return $this->whereNotIn('device_id',$deviceAtt)->select(explode(',', $fields))->get();
    }
}