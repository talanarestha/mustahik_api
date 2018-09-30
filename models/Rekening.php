<?php
class Rekening extends \Phalcon\Mvc\Model {

	protected $tablename = 't_rekening';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getActive($cabang){
        $query = "SELECT no_rekening as no_rek, bank FROM {$this->tablename} WHERE status_aktif=1 AND IDCabang=?";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC,[$cabang]);
        
        if ( !$result )
            return false;
        
        return $result;
    }
}