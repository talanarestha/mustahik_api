<?php
class Pekerjaan extends \Phalcon\Mvc\Model {

	protected $tablename = 't_pekerjaan';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getActive(){
        $query = "SELECT IDPekerjaan as id, Pekerjaan as pekerjaan FROM {$this->tablename} WHERE status=1 ORDER BY IDPekerjaan ASC";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        return $result;
    }
}