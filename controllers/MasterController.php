<?php

class MasterController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }


    /**
     * API get master data pendidikan
     */
    public function pendidikan() {
        $model = new Pendidikan;
        // get list muzaki
        $lists = $model->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }

    /**
     * API get master data pekerjaan
     */
    public function pekerjaan() {
        $model = new Pekerjaan;
        // get list muzaki
        $lists = $model->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }

    /**
     * API get master data mata uang
     */
    public function currency() {
        $model = new Currency;
        // get list muzaki
        $lists = $model->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }

    /**
     * API get master data rekening
     */
    public function rekening() {
        // input
        $cabang = $this->getPost('cabang');

        $model = new Rekening;
        // get list muzaki
        $lists = $model->getActive($cabang);
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }
}
