<?php

class MuzakiController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }

    /**
     * API get muzaki
     */
    public function get() {
        // input
        $page   = $this->getPost('page', 1);
        $limit  = $this->getPost('limit', 10);
        $filter = $this->getPost('filter');

        $muzaki = new Muzaki;
        // get list muzaki
        $muzakiLists = $muzaki->getAll($page, $limit, $filter);
        if( !$muzakiLists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($muzakiLists);
    }

    /**
     * API register personal muzaki
     */
    public function regPersonal() {
        // input
        $muzaki['salut']          = $this->getPost('salut');
        $muzaki['gelar']          = $this->getPost('gelar');
        $muzaki['nama']           = $this->getPost('nama');
        $muzaki['tempatLahir']    = $this->getPost('tempat_lahir');
        $muzaki['tanggalLahir']   = $this->getPost('tanggal_lahir');
        $muzaki['jenisKelamin']   = $this->getPost('jenis_kelamin');
        $muzaki['pendidikan']     = $this->getPost('pendidikan');
        $muzaki['pekerjaan']      = $this->getPost('pekerjaan');
        $muzaki['alamat']         = $this->getPost('alamat');
        $muzaki['kabupaten']      = $this->getPost('kabupaten');
        $muzaki['propinsi']       = $this->getPost('propinsi');
        $muzaki['kodePos']        = $this->getPost('kode_pos');
        $muzaki['telepon']        = $this->getPost('telepon');
        $muzaki['handphone']      = $this->getPost('handphone');
        $muzaki['email']          = $this->getPost('email');
        $muzaki['namaBank']       = $this->getPost('nama_bank');
        $muzaki['rekening']       = $this->getPost('rekening');
        $muzaki['npwp']           = $this->getPost('npwp');
        $muzaki['operator']       = $this->getPost('opt');
        $muzaki['cabang']         = $this->getPost('cabang');
        $muzaki['upz']            = $this->getPost('upz');
        $muzaki['ws']             = $this->getPost('ws');

        $this->log->info( abs( substr(microtime(1)-$this->starttime,0,8) ).' - MUZAKI - ' . json_encode($muzaki) );

        // validate mandatory input
        $valid = $this->validMandatory([$muzaki['nama'], $muzaki['jenisKelamin'], $muzaki['operator'], $muzaki['cabang'], $muzaki['upz'], $muzaki['ws']]);
        if( !$valid ) $this->respNOK('parameter tidak lengkap');

        // validate email format
        if( !empty($muzaki['email']) ){
            if (!filter_var($muzaki['email'], FILTER_VALIDATE_EMAIL)) {
                $this->respNOK('cek kembali format email kamu');
            }
        }

        // inbox notification
        $inbox = new Inbox;
        $message = array(
            'from'      => 'system',
            'to'        => $muzaki['operator'],
            'subject'   => 'Pendaftaran muzaki perorangan'
        );

        $model = new Muzaki;
        // register
        $result = $model->regPersonal($muzaki);
        if( !$result ) {
            $message['body'] = 'Pendaftaran muzaki tidak berhasil. Terjadi kesalahan pada sistem, coba lagi beberapa saat lagi';
            $inbox->compose($message);
            $this->respNOK('terjadi kesalahan sistem, coba kembali beberapa saat lagi');
        }
        
        $message['body'] = 'Pendaftaran muzaki atas nama '.$muzaki['nama'].' berhasil';
        $inbox->compose($message);

        $this->respOK();
    }
}
