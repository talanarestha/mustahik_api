<?php
class Penerimaan extends \Phalcon\Mvc\Model {

    protected $tablename = 't_penerimaan';
    protected $tableDetail = 't_penerimaan_detail';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}

    public function getAll($username, $page, $limit=10, $filter=null){
        $sqlFilter = '';
        if( $filter!=null ){
            $sqlFilter = ' AND a.kode_muzaki like "%'.$filter.'%" OR a.tanggal_terima like "'.$filter.'%" OR a.penyetor like "%'.$filter.'%"';
        }

        $offset     = ($page-1) * $limit; 
        $sqlLimit   = " LIMIT $offset,$limit";

        $query = "SELECT 
                a.IDTerima, 
                a.kode_muzaki, 
                a.tanggal_terima, 
                a.total, 
                a.cashback, 
                a.keterangan,
                a.penyetor, 
                a.IDCabang,
                a.id_upz, 
                a.id_event, 
                a.ws, 
                a.email, 
                a.hamba_allah, 
                a.created_by,
                b.*,
                c.nama as nama_muzaki,
                c.nik as nik,
                c.alamat as alamat,
                c.npwp as npwp,
                (SELECT jenis_penerimaan FROM t_jenis_penerimaan WHERE IDJenis=b.IDJenis) as category,
                IF(b.IDProgram<>0,(SELECT jenis_penerimaan FROM t_jenis_penerimaan WHERE IDJenis=b.IDProgram),'') as subcategory
            FROM {$this->tablename} a 
            LEFT JOIN {$this->tableDetail} b ON a.IDTerima=b.IDTerima 
            LEFT JOIN t_muzaki c ON a.kode_muzaki=c.kode_muzaki
            WHERE a.created_by='$username' $sqlFilter ORDER BY a.created_on DESC $sqlLimit";
        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        $data = array();
        $root = array('kode_muzaki', 'tanggal_terima', 'total', 'cashback', 'keterangan', 'penyetor', 'IDCabang',
            'id_upz', 'id_event', 'ws', 'email', 'hamba_allah', 'created_by', 'nama_muzaki', 'alamat', 'nik', 'npwp'
        );

        foreach( $result as $row ){

            if( !isset($data[$row['IDTerima']]) ) {
                $data[$row['IDTerima']] = array(
                    'IDTerima'          => $row['IDTerima'],
                    'kode_muzaki'       => $row['kode_muzaki'],
                    'nama_muzaki'       => $row['nama_muzaki'],
                    'alamat'            => $row['alamat'],
                    'nik'               => $row['nik'],
                    'npwp'              => $row['npwp'],
                    'tanggal_terima'    => $row['tanggal_terima'],
                    'total'             => $row['total'],
                    'cashback'          => $row['cashback'],
                    'keterangan'        => $row['keterangan'],
                    'penyetor'          => $row['penyetor'],
                    'IDCabang'          => $row['IDCabang'],
                    'id_upz'            => $row['id_upz'],
                    'id_event'          => $row['id_event'],
                    'ws'                => $row['ws'],
                    'email'             => $row['email'],
                    'hamba_allah'       => $row['hamba_allah'],
                    'created_by'        => $row['created_by']
                );
            }

            //unset
            foreach($root as $field){
                unset( $row[$field] );
            }

            $data[$row['IDTerima']]['donasi'][] = $row;
        }

        $final = array();
        foreach($data as $row){
            $final[] = $row;
        }

        return $final;
    }

    /**
     * Generate kode penerimaan
     * cabang = id cabang
     * upz    = id upz
     * ws     = no urut dari penempatan upz
     */
    private function generateKode($tgl, $cabang, $upz, $ws){
        $tgl = date('Y-m-d', strtotime( str_replace(array('T','Z'),array(' ', ''),$tgl) ) );
        $t   = explode("-",$tgl);
        $ws  = str_pad( $ws , 2, "0", STR_PAD_LEFT);
        
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
		return $cabang.str_pad( $upz , 2, "0", STR_PAD_LEFT).'-'.str_pad( $ws , 2, "0", STR_PAD_LEFT).'-'.substr($t[0],2,2).$t[1].$t[2] .'-'. $urut;
	}

    public function saveData($donasi){
        $code = $this->generateKode($donasi['tanggal'], $donasi['cabang'], $donasi['upz'], $donasi['ws']);
        // @todo: calculate total
        $donasi['total'] = 0; 
        foreach( $donasi['donasi'] as $row ){
            if( $row['bentuk']=='UANG' ){
                $donasi['total'] += $row['jumlah']; 
            }
        }

        $donasi['ws'] = str_pad( $donasi['ws'] , 2, "0", STR_PAD_LEFT);

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
                created_on,
                sync
            ) VALUES 
            ( 
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                %s,
                %s,
                %s,
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                %s,
                now(),
                'NEW'
            )",
            $this->tablename,
            $code,
            $donasi['muzakiCode'],
            date("Y-m-d H:i:s", strtotime( str_replace(array('T','Z'),array(' ', ''),$donasi['tanggal']) )),
            $donasi['total'],
            0,
            $this->db->escapeString($donasi['keterangan']),
            $this->db->escapeString($donasi['penyetor']),
            $this->db->escapeString($donasi['cabang']),
            $donasi['upz'],
            $donasi['event'],
            $donasi['ws'],
            '',
            $donasi['hambaallah'],
            $this->db->escapeString($donasi['username'])
        );

        $this->db->execute($query);        

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        foreach( $donasi['donasi'] as $row  ){
            $this->saveDetail($code, $donasi['username'], $row);
        }

        return $code;
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
                %s,
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                %s,
                %s,
                %s,
                %s,
                now()
            )",
            $this->tableDetail,
            $donasiId,
            $donasi['category_id'],
            $jenis,
            '',
            ( empty($donasi['sub_category']) ) ? '0' : $this->db->escapeString($donasi['sub_category']),
            $donasi['currency'],
            ( $donasi['rekening']!='TUNAI' ) ? $donasi['jumlah'] : '0',
            ( $donasi['rekening']=='TUNAI' ) ? $donasi['jumlah'] : '0',
            $donasi['jumlah'],
            ( isset($donasi['edc']) && $donasi['edc']==1 ) ? 'Y' : 'N',
            ( $donasi['rekening']!='TUNAI' ) ? $donasi['rekening'] : '',
            ( $donasi['bentuk']=='BARANG' ) ? $this->db->escapeString($donasi['nama_barang']) : 'null',
            ( $donasi['bentuk']=='BARANG' ) ? $this->db->escapeString($donasi['jumlah']) : 'null',
            ( $donasi['bentuk']=='BARANG' ) ? $this->db->escapeString($donasi['satuan_barang']) : 'null',
            $this->db->escapeString($username)
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