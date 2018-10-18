<?php

Class MustahikHelper {

	/* Jenis Asnaf */
	private $asnaf = [
		'fm_pendidikan'	=> 'FM.Penddikan',
		'fm_obat'		=> 'FM.Obat',
		'fm_usaha'		=> 'FM.Usaha',
		'fm_hidup'		=> 'FM.Hidup',
		'ghorim'		=> 'Ghorim',
		'fisabilillah'	=> 'Fisabilillah',
		'ibnu_sabil'	=> 'Ibnu Sabil',
		'muallaf'		=> 'Muallaf',
	];

	/* Status Pengajuan */
	private $statusPengajuan = [
		'0'				=> 'Dalam Proses',
		'1'				=> 'Diterima',
		'-1'			=> 'Ditolak'
	];

	/* Jenis Bantuan */
	private $jenisBantuan = [
		'insidentil'	=> 'Insindentil',
		'rutin'			=> 'Rutin',
		'pemberdayaan'	=> 'Pemberdayaan',
		'biasa'			=> 'Biasa'
	];

	/* Bentuk Bantuan */
	private $bentukBantuan = [
		'uang'			=> 'Uang',
		'barang'		=> 'Barang',
	];

	/* Jenis Kelamin */
	private $jenisKelamin = [
		'L'				=> 'Laki-laki',
		'P'				=> 'Perempuan',
	];

	/* Status Nikah */
	private $statusNikah = [
		'belum'			=> 'Belum Menikah',
		'duda-janda'	=> 'Duda / Janda',
		'menikah'		=> 'Menikah'
	];

	/* Jenis Lampiran */
	private $jenisLampiran = [
		'kk'			=> 'Kartu Keluarga',
		'pengantar'		=> 'Surat Pengantar',
		'pernyataan'	=> 'Surat Penyataan',
		'ktp'			=> 'KTP',
		'lainnya'		=> 'Lainnya'
	];

	/* Waktu Survey */
	private $waktuSurvey = [
		'<1pekan'		=> '< 1 Pekan',
		'2pekan'		=> '2 Pekan',
		'3pekan'		=> '3 Pekan',
		'>4pekan'		=> '> 4 Pekan'
	];

	/* Tindak Lanjut */
	private $tindakLanjut = [
		'monitoring'	=> 'Monitoring',
		'tidak'			=> 'Tidak',
	];

	/* Kelayakan */
	private $kelayakan = [
		'layak'			=> 'Layak Dibantu',
		'tidak'			=> 'Tidak Layak Dibantu',
		'perhatian'		=> 'Perlu Perhatian Khusus'
	];

	/* Status Rumah */
	private $statusRumah = [
		'sendiri'		=> 'Rumah Sendiri',
		'kontrak'		=> 'Mengontrak',
		'orangtua'		=> 'Rumah Orangtua',
		'tidakada'		=> 'Tidak punya tempat tinggal'
	];

	private function _getArrayValue ($arrayList, $arrayKey, $default = "")
	{
		return isset($arrayList[$arrayKey]) ? 
			$arrayList[$arrayKey] : $default;
	}

	private function _getOptionList ($arrayList)
	{
        $options = [];
        foreach ($arrayList as $key => $val)
        {
            $options[] = [
                'id'    => $key,
                'name'  => $val
            ];
        }
		return $options;
	}

	private function _getListOrValue ($list, $as_option)
	{
		if ($as_option)
		{
			return $this->_getOptionList ($list);
		}
		else
		{
			return $list;
		}
	}

    public function formatTanggal ($timestamp, $short = true)
    {
        if (empty($timestamp) || $timestamp == '0000-00-00') 
            return '-';

        if (!is_numeric($timestamp))
            $timestamp = strtotime($timestamp);
        
		$bulan = array ("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September", "Oktober", "Nopember", "Desember");
		$hari = ['Ahad','Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", "Sabtu"];

        $mon = date("n", $timestamp) -1 ;
        $day = date("d", $timestamp);
		$year= date("Y", $timestamp);
		$hr= date("w", $timestamp);

        $namabulan = $bulan[$mon];
        if ($short) $namabulan = substr($namabulan,0, 3);
        
        return $hari[$hr].", ".$day.' '.$namabulan.' '.$year;
    }

	/* get Asnaf */
	public function getAsnaf ($asnaf_id)
	{
		return $this->_getArrayValue($this->asnaf, $asnaf_id);
	}

	public function getAsnafList ($as_option = true)
	{
		return $this->_getListOrValue ($this->asnaf, $as_option);
	}

	/* get Jenis Bantuan */
	public function getJenisBantuan ($jenis)
	{
		return $this->_getArrayValue($this->jenisBantuan, $jenis);
	}

	public function getJenisBantuanList ($as_option = true)
	{
        return $this->_getListOrValue ($this->jenisBantuan, $as_option);
	}

	/* get Bentuk Bantuan */
	public function getBentukBantuan ($jenis)
	{
		return $this->_getArrayValue($this->bentukBantuan, $jenis);
	}

	public function getBentukBantuanList ($as_option = true)
	{
        return $this->_getListOrValue ($this->bentukBantuan, $as_option);
	}

	/* Status Pengajuan */
	public function getStatusPengajuan ($status)
	{
		return $this->_getArrayValue($this->statusPengajuan, $status);
	}

	public function getStatusPengajuanList ($as_option = true)
	{
        return $this->_getListOrValue ($this->statusPengajuan, $as_option);
	}

	public function getStatusNikah ($status)
	{
		return $this->_getArrayValue($this->statusNikah, $status);
	}

	public function getStatusNikahList ($as_option = true)
	{
        return $this->_getListOrValue ($this->statusNikah, $as_option);
	}

	public function getJenisKelamin ($status)
	{
		return $this->_getArrayValue($this->jenisKelamin, $status);
	}

	public function getJenisKelaminList ($as_option = true)
	{
        return $this->_getListOrValue ($this->jenisKelamin, $as_option);
	}

	public function getJenisLampiran ($jenis)
	{
		return $this->_getArrayValue($this->jenisLampiran, $jenis);
	}

	public function getJenisLampiranList ($as_option = true)
	{
		return $this->_getListOrValue ($this->jenisLampiran, $as_option);
	}

	public function getWaktuSurvey ($waktu)
	{
		return $this->_getArrayValue($this->waktuSurvey, $waktu);
	}

	public function getWaktuSurveyList ($as_option = true)
	{
		return $this->_getListOrValue ($this->waktuSurvey, $as_option);
	}

	public function getTindakLanjut ($waktu)
	{
		return $this->_getArrayValue($this->tindakLanjut, $waktu);
	}

	public function getTindakLanjutList ($as_option = true)
	{
		return $this->_getListOrValue ($this->tindakLanjut, $as_option);
	}

	public function getKelayakan ($waktu)
	{
		return $this->_getArrayValue($this->kelayakan, $waktu);
	}

	public function getKelayakanList ($as_option = true)
	{
		return $this->_getListOrValue ($this->kelayakan, $as_option);
	}

	public function getStatusRumah ($status)
	{
		return $this->_getArrayValue($this->statusRumah, $status);
	}

	public function getStatusRumahList ($as_option = true)
	{
		return $this->_getListOrValue ($this->statusRumah, $as_option);
	}

	private function _setAlamatLengkap ($record)
	{
		$alamt_lengkap = $this->_getArrayValue($record, 'alamat','');
		if ($alamt_rt = $this->_getArrayValue($record, 'alamat_rt',''))
			$alamt_lengkap .= " RT ".$alamt_rt ;

		if ($alamt_rw = $this->_getArrayValue($record, 'alamat_rw',''))
			$alamt_lengkap .= " RW ".$alamt_rw ;

		if ($desa = $this->_getArrayValue($record, 'desa',''))
			$alamt_lengkap .= ", Desa/Kelurahan ".$desa ;

		if ($kecamatan = $this->_getArrayValue($record, 'kecamatan',''))
			$alamt_lengkap .= ". Kecamatan ".$kecamatan ;

		if ($kokab = $this->_getArrayValue($record, 'kokab',''))
			$alamt_lengkap .= ", ".$kokab ;

		if ($propinsi = $this->_getArrayValue($record, 'propinsi',''))			
			$alamt_lengkap .= ', ' .$propinsi;

		return ucwords(strtolower($alamt_lengkap));
	}

	public function normalizePengajuan ($pengajuan)
	{
		if (isset($pengajuan['jumlah_pengajuan']))
			$pengajuan['jumlah_pengajuan_text'] = number_format($pengajuan['jumlah_pengajuan'],0);

		$pengajuan['tanggal_text'] = $this->formatTanggal(isset($pengajuan['tanggal']) ? $pengajuan['tanggal'] : '');
		$pengajuan['tanggal_konfirmasi_text'] = $this->formatTanggal($this->_getArrayValue($pengajuan, 'tanggal_konfirmasi'));
		$pengajuan['tanggal_lahir_text'] = $this->formatTanggal($this->_getArrayValue($pengajuan, 'tanggal_lahir'));
			
		$pengajuan['jenis_permohonan_text'] = $this->_getArrayValue($this->asnaf, $this->_getArrayValue($pengajuan, 'jenis_permohonan','-'));
		$pengajuan['jenis_bantuan_text'] = $this->_getArrayValue($this->jenisBantuan, $this->_getArrayValue($pengajuan, 'jenis_bantuan','-'));
		$pengajuan['jenis_kelamin_text'] = $this->_getArrayValue($this->jenisKelamin, $this->_getArrayValue($pengajuan, 'jenis_kelamin','-'));
		$pengajuan['status_text'] = $this->_getArrayValue($this->statusPengajuan, $this->_getArrayValue($pengajuan, 'status','-'));
		$pengajuan['status_nikah_text'] = $this->_getArrayValue($this->statusNikah, $this->_getArrayValue($pengajuan, 'status_nikah','-'));
		$pengajuan['rumah_text'] = $this->_getArrayValue($this->statusRumah, $this->_getArrayValue($pengajuan, 'rumah','-'));
			
		if (isset($pengajuan['survey']))
			$pengajuan['survey_text'] = $pengajuan['survey'] == 1 ? 'Ya' : 'Tidak';

		$pengajuan['alamat_lengkap'] = $this->_setAlamatLengkap ($pengajuan);

		return $pengajuan;
	}

	public function normalizeMustahik ($mustahik)
	{
		$mustahik['tanggal_lahir_text'] = $this->formatTanggal($this->_getArrayValue($mustahik, 'tanggal_lahir'));
			
		$mustahik['jenis_kelamin_text'] = $this->_getArrayValue($this->jenisKelamin, $this->_getArrayValue($mustahik, 'jenis_kelamin','-'));
		$mustahik['status_nikah_text'] = $this->_getArrayValue($this->statusNikah, $this->_getArrayValue($mustahik, 'status_nikah','-'));
		$mustahik['rumah_text'] = $this->_getArrayValue($this->statusRumah, $this->_getArrayValue($mustahik, 'rumah','-'));
		$mustahik['alamat_lengkap'] = $this->_setAlamatLengkap ($mustahik);
			
		return $mustahik;
	}

	public function normalizePengajuanSurvey ($survey)
	{	
		$survey['kelayakan_text'] = $this->_getArrayValue($this->kelayakan, $this->_getArrayValue($survey, 'kelayakan','-'));
		$survey['waktu_survey_text'] = $this->_getArrayValue($this->waktuSurvey, $this->_getArrayValue($survey, 'waktu_survey','-'));
		
		return $survey;
	}

}