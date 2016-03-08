<?php

class Weixin_Shake_PageModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = "think_%s_shake_page";

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
                    $table->text('page_url');
                    $table->text('icon_url');
                    $table->char('title',12);
                    $table->char('description',14);
                    $table->char('comment',30);
                    $table->longText('device_ids')->nullable()->default(null);
                    $table->timestamp('create_time')->nullable()->default(null);
                    $table->engine = "InnoDB";
                    $table->charset = "utf8";
                });
        }
    }
}