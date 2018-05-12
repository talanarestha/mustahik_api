<?php
class Category extends \Phalcon\Mvc\Model {

	protected $tablename = 't_jenis_penerimaan';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getActive($parentId){
        $query = "SELECT IDJenis as id, pid, kode, jenis_penerimaan as name FROM {$this->tablename} WHERE status_aktif=1 AND pid=? ORDER BY IDJenis ASC";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC, [$parentId]);
        
        if ( !$result )
            return false;
        
        return $result;
    }
}