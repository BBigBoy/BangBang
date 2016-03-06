<?php

class Weixin_JSTicketModel
{
    private $tableName = 'think_jsapi_ticket';
    private $jsTicketDb;

    /**
     * Weixin_JSTicketModel constructor.
     */
    public function __construct()
    {
        $this->jsTicketDb = new Db_Mysql();
    }

    public function findAuthorizer($whereAuthorizer)
    {
        return $this->jsTicketDb->get_row($this->tableName, $whereAuthorizer);
    }

    public function addTicketInfo($ticketData)
    {
        return $this->jsTicketDb->insert($this->tableName,$ticketData);
    }

    public function updateTiketInfo($ticketData, $whereAuthorizer)
    {
        return $this->jsTicketDb->update($this->tableName,$ticketData,$whereAuthorizer);
    }


}