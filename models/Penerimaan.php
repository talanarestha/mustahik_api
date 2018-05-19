<?php
class Penerimaan extends \Phalcon\Mvc\Model {

    protected $tablename = 't_penerimaan';
    protected $tableDetail = 't_penerimaan_detail';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}


    /**
     * Generate kode penerimaan
     * cabang = id cabang
     * upz    = id upz
     * ws     = no urut dari penempatan upz
     */
    private function generateKode($tgl, $cabang, $upz, $ws){
        $tgl = date('Y-m-d', strtotime($tgl));
        $t   = explode("-",$tgl);
        
		$result = $this->db->fetchOne( "SELECT 
                LPAD( COUNT(*)+1, 3,'0') as urut
            FROM t_penerimaan
            WHERE YEAR(tanggal_terima) = YEAR('".$tgl."')
               AND MONTH(tanggal_terima) = MONTH('".$tgl."')
               AND DAY(tanggal_terima) = DAY('".$tgl."')
               AND IDCabang='$cabang' AND id_upz='$upz' AND ws='$ws'"
        );
		$urut = 1;
        if( $result ) $urut = $result['urut'];
		return $cabang.str_pad( $upz , 2, "0", STR_PAD_LEFT).'-'.$ws.'-'.substr($t[0],2,2).$t[1].$t[2] .'-'. $urut;
	}

    public function saveData($donasi){
        $code = $this->generateKode($donasi['tanggal'], $donasi['cabang'], $donasi['upz'], $donasi['ws']);
        // @todo: calculate total
        $donasi['total'] = 0; 

        $query = sprintf("INSERT INTO %s ( 
                IDTerima,
                kode_muzaki,
                tanggal_terima,
                total,
                cashback,
                keterangan,
                penyetor,
                IDCabang,
                id_upz,
                id_event,
                ws,
                email,
                hamba_allah,
                created_by,
                created_on
            ) VALUES 
            ( 
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                now()
            )",
            $this->tablename,
            $code,
            $donasi['muzakiCode'],
            date("Y-m-d H:i:s", strtotime($donasi['tanggal'])),
            $donasi['total'],
            0,
            $donasi['keterangan'],
            $donasi['penyetor'],
            $donasi['cabang'],
            $donasi['upz'],
            $donasi['event'],
            $donasi['ws'],
            '',
            $donasi['hambaallah'],
            $donasi['username']
        );

        $this->db->execute($query);        

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        foreach( $donasi['donasi'] as $row  ){
            $this->saveDetail($code, $donasi['username'], $row);
        }

        return true;
    }

    public function saveDetail($donasiId, $username, $donasi){
        $jenis = 'barang';
        if( $donasi['bentuk']=='UANG' ){
            $jenis = ($donasi['rekening'] == 'TUNAI') ? 'tunai' : 'bank';
        }
        
        $query = sprintf("INSERT INTO %s ( 
                IDTerima,
                IDJenis,
                IDDetailJenis,
                jenis,
                IDProgram,
                mata_uang,
                bank,
                tunai,
                jumlah,
                edc, 
                rekening,
                nama_barang,
                jumlah_barang,
                satuan,
                created_by,
                created_on
            ) VALUES 
            ( 
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                now()
            )",
            $this->tableDetail,
            $donasiId,
            $donasi['category_id'],
            $jenis,
            '',
            ( empty($donasi['sub_category']) ) ? '0' : $donasi['sub_category'],
            $donasi['currency'],
            '0',
            ( $donasi['rekening']=='TUNAI' ) ? $donasi['jumlah'] : '0',
            ( $donasi['rekening']!='TUNAI' ) ? $donasi['jumlah'] : '0',
            'N', 
            ( $donasi['rekening']!='TUNAI' ) ? $donasi['rekening'] : '',
            ( $donasi['bentuk']=='BARANG' ) ? $donasi['nama_barang'] : '',
            ( $donasi['bentuk']=='BARANG' ) ? $donasi['jumlah'] : '0',
            ( $donasi['bentuk']=='BARANG' ) ? $donasi['satuan_barang'] : '0',
            $username
        );

        $this->db->execute($query);        

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        $id = $this->db->lastInsertId();
        $this->updateJenisDetail( $id, $id.$jenis);

        return true;
    }

    public function updateJenisDetail($id, $detailjenis){
        $query = sprintf("UPDATE %s SET IDDetailJenis='%s' WHERE IDDetail=%d",
            $this->tableDetail,
            $detailjenis,
            $id
        );

        $this->db->execute($query);        

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        return true;
    }
}