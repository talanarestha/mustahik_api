<?php
class Users extends \Phalcon\Mvc\Model {

	protected $tablename = 't_user';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function byUsername($username){
        $query = "SELECT a.*, b.id_upz, b.no_urut, b.koordinator, c.nama_upz, d.id_event, d.nama_event, d.tanggal_mulai, d.tanggal_selesai, e.NamaCabang, f.NamaJabatan
            FROM {$this->tablename} a 
            LEFT JOIN t_penempatan b ON a.username=b.username 
            LEFT JOIN t_upz c ON b.id_upz=c.id_upz
            LEFT JOIN t_event d ON b.id_event=d.id_event AND DATE(now()) BETWEEN DATE(tanggal_mulai) AND DATE(tanggal_selesai)
            LEFT JOIN t_cabang e ON a.IDCabang=e.IDCabang
            LEFT JOIN t_jabatan f ON a.IDJabatan=f.IDJabatan
            WHERE a.username=?";
        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC, [$username]);
        
        if ( !$result )
            return false;
        
        $event = array();
        $profile = array();
        foreach($result as $row){
            if( $row['id_event']!='' ){
                $event[] = array(
                    'IDCabang'          => $row['IDCabang'],
                    'NamaCabang'        => $row['NamaCabang'],
                    'id_upz'            => $row['id_upz'],
                    'nama_upz'          => $row['nama_upz'],
                    'no_urut'           => $row['no_urut'],
                    'id_event'          => $row['id_event'],
                    'nama_event'        => $row['nama_event'],
                    'tanggal_mulai'     => $row['tanggal_mulai'],
                    'tanggal_selesai'   => $row['tanggal_selesai']
                );
            }
        }
        $profile = $result[0];
        unset($profile['id_event']);
        unset($profile['nama_event']);
        unset($profile['tanggal_mulai']);
        unset($profile['tanggal_selesai']);
        unset($profile['IDCabang']);
        unset($profile['NamaCabang']);
        unset($profile['id_upz']);
        unset($profile['nama_upz']);
        unset($profile['no_urut']);
        $profile['event'] = $event;
        return $profile;
    }
}