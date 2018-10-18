<?php
class Pengajuan extends Base {

    protected $tablename = 'pengajuan';
    protected $keys = ['id'];

    public function getById($id) 
    {
        $result = $this->db->fetchOne(
            "SELECT p.*, a.user_fullname as menyetujui_text , t.user_fullname as penerima_text, 
            prop.nama as propinsi, kokab.nama as kokab, kec.nama as kecamatan, desa.nama as desa
            FROM {$this->tablename} p 
            LEFT JOIN users a ON p.menyetujui=a.user_id 
            LEFT JOIN users t ON p.penerima=t.user_id 

            LEFT JOIN wilayah_propinsi prop ON prop.kode=p.alamat_propinsi
            LEFT JOIN wilayah_kokab kokab ON kokab.kode=p.alamat_kokab
            LEFT JOIN wilayah_kecamatan kec ON kec.kode=p.alamat_kecamatan
            LEFT JOIN wilayah_desa desa ON desa.kode=p.alamat_desa
            WHERE p.id=?", 
            Phalcon\Db::FETCH_ASSOC, array($id)
        );
        
        if ( !$result ) {
            return false;
        }
        
        return $result;
    }

    public function getListBy ($aCondition, $start, $offset = 15)
    {
		$condition = $this->setCondition($aCondition);

        $result = $this->db->fetchAll(
            "SELECT pengajuan.* FROM {$this->tablename} 
            LEFT JOIN pengajuan_survey ON pengajuan_survey.pengajuan_id=pengajuan.id
            WHERE $condition 
            ORDER BY pengajuan.id
            LIMIT $start,$offset", 
            Phalcon\Db::FETCH_ASSOC
        );

        return $result;
    }

    public function genId() {

        // Create a random user id between 1200 and 4294967295
        $randomUniqueInt = date('ymdHis') . substr(microtime(true),-4) . mt_rand( 100, 999 );

        $result = $this->getById( $randomUniqueInt );
        if( $result ){
            return $this->genId();
        }

        return $randomUniqueInt;
    }

    public function setMustahik($id, $mustahik_id){
        $pengajuan = new stdClass;
        $pengajuan->id = $id;
        $pengajuan->mustahik_id = $mustahik_id;

        return $this->updateRecord($pengajuan);
        /* $this->db->execute(sprintf("REPLACE INTO mustahik_pengajuan SET pengajuan_id='%s', mustahik_id='%s'",
            $id, $mustahik_id
        ));
        
        if ($this->db->affectedRows() == 0) {
            return false;
        }
        return true; */
    }

    public function getByMustahik($mustahik_id) {
        
        return $this->getRecordBy(["mustahik_id='$mustahik_id'"]);
        /* $result = $this->db->fetchAll(
            "SELECT p.* FROM {$this->tablename} p LEFT JOIN mustahik_pengajuan mp ON p.id=mp.pengajuan_id WHERE mp.mustahik_id=?", 
            Phalcon\Db::FETCH_ASSOC, array($mustahik_id)
        );
        
        if ( !$result ) {
            return false;
        }
        
        return $result; */
    }
}