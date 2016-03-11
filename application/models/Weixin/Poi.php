<?php

class Weixin_PoiModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_poi";

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
                    $table->integer('id',true);
                    $table->integer('sid');
                    $table->string('business_name', 50);
                    $table->string('branch_name', 50);
                    $table->string('address', 100);
                    $table->string('telephone', 20);
                    $table->string('categories', 100);
                    $table->string('city', 30);
                    $table->string('province', 30);
                    $table->integer('offset_type');
                    $table->string('longitude', 20);
                    $table->string('latitude', 20);
                    $table->text('photo_list');
                    $table->text('introduction');
                    $table->text('recommend');
                    $table->text('special');
                    $table->string('open_time', 20);
                    $table->integer('avg_price');
                    $table->integer('poi_id');
                    $table->integer('available_state');
                    $table->string('district', 20);
                    $table->integer('update_status');
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }

    public function delAll()
    {
        $this->truncate();
    }

    public function addAll($poiList)
    {
        return $this->insert($poiList);
        /*///只是通過循環實現了批量導入
        $addtate = true;
        foreach ($poiList as $poi) {
            $addtate = $this->insert($poi);
            if (!$addtate) {
                return $addtate;
            }
        }
        return $addtate;*/
    }

    public function findMultiPoi($wherePoi, $fields = '*')
    {
        return $this->where($wherePoi)->select(explode(',',$fields))->get();
    }
}