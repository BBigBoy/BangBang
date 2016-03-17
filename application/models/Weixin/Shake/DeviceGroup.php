<?php

class Weixin_Shake_DeviceGroupModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_device_group";

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
                    $table->integer('group_id', false, true);
                    $table->primary('group_id');
                    $table->string('group_name', 100);
                    $table->longText('device_ids')->nullable()->default(null);
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }

    public function delAll()
    {
        $this->truncate();
    }

    public function countNum()
    {
        return $this->count();
    }

    public function addAll($groups)
    {
        return $this->insert($groups);
    }

    function selectNullDeviceGroup()
    {
        return $this->whereNull('device_ids')->select('group_id')->get();
    }

    function updateGroup($whereGroup, $groupInfo)
    {
        return $this::update($whereGroup, $groupInfo);
    }

}