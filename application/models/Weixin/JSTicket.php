<?php

class Weixin_JSTicketModel extends \Illuminate\Database\Eloquent\Model
{
    public $timestamps = false;
    protected $table = 'think_jsapi_ticket';

    public function findAuthorizer($whereAuthorizer)
    {
        return $this::where($whereAuthorizer)->first();
    }

    public function addTicketInfo($ticketData)
    {
        return $this::insert($ticketData);
    }

    public function updateTiketInfo($ticketData, $whereAuthorizer)
    {
        return $this::update($whereAuthorizer, $ticketData);
    }


}