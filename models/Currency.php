<?php
class Currency extends \Phalcon\Mvc\Model {

	protected $tablename = 't_mata_uang';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getActive(){
        $query = "SELECT id_mata_uang as id, mata_uang as currency, keterangan as descr FROM {$this->tablename} ORDER BY urut ASC";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        return $result;
    }
}