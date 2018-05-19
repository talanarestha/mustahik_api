<?php

class DonasiController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }


    /**
     * API get jenis/kategori donasi
     */
    public function category() {
        // input
        $parentId   = $this->getPost('parentid');

        $category = new Category;
        // get list muzaki
        $lists = $category->getActive($parentId);
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }


    /**
     * API post donasi
     */
    public function submit() {
        // input
        $donasi['muzakiCode'] = $this->getPost('muzaki_code');
        $donasi['tanggal']    = $this->getPost('date');
        $donasi['keterangan'] = $this->getPost('keterangan');
        $donasi['penyetor']   = $this->getPost('penyetor');
        $donasi['event']      = $this->getPost('event');
        $donasi['email']      = $this->getPost('email');
        $donasi['hambaallah'] = $this->getPost('hamba_allah');
        $donasi['kwitansi']   = $this->getPost('kwitansi');
        $donasi['username']   = $this->getPost('username');
        $donasi['donasi']     = is_array($this->getPost('donasi')) ? json_encode($this->getPost('donasi')) : '';
        $donasi['cabang']     = $this->getPost('cabang');
        $donasi['upz']        = $this->getPost('upz');
        $donasi['ws']         = $this->getPost('ws');

        // validate mandatory input
        $valid = $this->validMandatory([
            $donasi['username'], $donasi['donasi'], $donasi['muzakiCode'], $donasi['event'], $donasi['cabang'], $donasi['upz'], $donasi['ws']
        ]);
        if( !$valid ) $this->respNOK('missing mandatory');

        $donasiArr = json_decode($donasi['donasi'],1);
        if( !$donasiArr ) $this->respNOK('missing mandatory donasi');

        $donasi['donasi'] = $donasiArr;

        // get info agent by username
        // $users = new Users;
        // $agent = $users->byUsername($donasi['username']);

        // if( !$agent ) $this->respNOK('agent not found');

        // $donasi['cabang'] = $agent['IDCabang'];
        // $donasi['upz']    = $agent['id_upz'];
        // $donasi['ws']     = $agent['no_urut'];

        $model = new Penerimaan;
        // submit penerimaan
        $result = $model->saveData($donasi);
        if( !$result ) $this->respNOK('server error, mohon coba kembali');

        $this->respOK();
    }
}
