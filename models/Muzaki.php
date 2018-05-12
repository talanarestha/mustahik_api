<?php
class Muzaki extends \Phalcon\Mvc\Model {

	protected $tablename = 't_muzaki';
	protected $db; // db instance

    public function onConstruct() {
    	$this->db = $this->getDi()->getShared('db');
	}
	
	public function getAll($page, $limit=10, $filter=null){
        $sqlFilter = '';
        if( $filter!=null ){
            $sqlFilter = ' AND kode_muzaki like "'.$filter.'%" OR no_kartu like "'.$filter.'%" OR nama like "%'.$filter.'%"';
        }

        $offset     = ($page-1) * $limit; 
        $sqlLimit   = " LIMIT $offset,$limit";

        $query = "SELECT * FROM {$this->tablename} WHERE 1=1 $sqlFilter ORDER BY created_on DESC $sqlLimit";

        $result = $this->db->fetchAll($query, \Phalcon\Db::FETCH_ASSOC);
        
        if ( !$result )
            return false;
        
        return $result;
    }

    /**
     * Generate kode muzaki
     * cabang = id cabang
     * upz    = id upz
     * ws     = no urut dari penempatan upz
     */
    private function generateKode($cabang, $upz, $ws){
		$result = $this->db->fetchOne( "SELECT kode_muzaki, CONVERT(SUBSTRING_INDEX( substr(kode_muzaki,8,5) ,'-',-1),UNSIGNED INTEGER)+1 as urut
			FROM t_muzaki
			WHERE  IDCabang='$cabang' AND id_upz='$upz' AND ws='$ws'
			order by kode_muzaki desc
            limit 1"
        );
		$urut = 1;
        if( $result ) $urut = $result['urut'];
		return $cabang.str_pad( $upz , 2, "0", STR_PAD_LEFT).$ws.str_pad( $urut , 5, "0", STR_PAD_LEFT);
	}

    public function regPersonal($muzaki){
        $query = sprintf("INSERT INTO %s ( 
                kode_muzaki,
                jenis_muzaki,
                email,
                `password`,
                status_aktivasi,
                salut,
                gelar,
                nama,
                alamat,
                kabupaten,
                propinsi,
                kode_pos,
                telepon,
                handphone,
                tmp_lahir,
                tgl_lahir,
                jenis_kelamin,
                pendidikan_terakhir,
                pekerjaan,
                nama_bank,
                no_rekening,
                npwp,
                IDCabang,
                id_upz,
                ws,
                created_on,
                created_by
            ) VALUES 
            ( 
                '%s',
                'Perorangan',
                '%s',
                '%s',
                'N',
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
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                now(),
                '%s'
            )",
            $this->tablename,
            $this->generateKode($muzaki['cabang'], $muzaki['upz'], $muzaki['ws']),
            $muzaki['email'],
            '',
            $muzaki['salut'],
            $muzaki['gelar'],
            $muzaki['nama'],
            $muzaki['alamat'],
            $muzaki['kabupaten'],
            $muzaki['propinsi'],
            $muzaki['kodePos'],
            $muzaki['telepon'],
            $muzaki['handphone'],
            $muzaki['tempatLahir'],
            date('Y-m-d', strtotime($muzaki['tanggalLahir'])),
            $muzaki['jenisKelamin'],
            $muzaki['pendidikan'],
            $muzaki['pekerjaan'],
            $muzaki['namaBank'],
            $muzaki['rekening'],
            $muzaki['npwp'],
            $muzaki['cabang'],
            $muzaki['upz'],
            $muzaki['ws'],
            $muzaki['operator']
        );

        try {
            $this->db->execute($query);
        } catch(Exception $e) {
            if( stripos( $e->getMessage(), 'SQLSTATE[23000]' ) !==false ){
                // duplicate 
                return 'DUPLICATE';
            }
        }

        if ($this->db->affectedRows() == 0) {
            return false;
        }

        return true;
    }
}