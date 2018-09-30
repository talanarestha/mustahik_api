<?php
class Pendidikan extends \Phalcon\Mvc\Model {

	protected $tablename = 't_pendidikan';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getActive(){
        $query = "SELECT IDPendidikan as id, Pendidikan as pendidikan FROM {$this->tablename} WHERE status=1 ORDER BY urut ASC";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        return $result;
    }
}