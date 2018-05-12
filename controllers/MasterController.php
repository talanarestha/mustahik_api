<?php

class MasterController extends ControllerBase {

    public function onConstruct() {
        parent::onConstruct();
    }


    /**
     * API get master data pendidikan
     */
    public function pendidikan() {
        $pendidikan = new Pendidikan;
        // get list muzaki
        $lists = $pendidikan->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }

    /**
     * API get master data pekerjaan
     */
    public function pekerjaan() {
        $pekerjaan = new Pekerjaan;
        // get list muzaki
        $lists = $pekerjaan->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }

    /**
     * API get master data mata uang
     */
    public function currency() {
        $currency = new Currency;
        // get list muzaki
        $lists = $currency->getActive();
        if( !$lists ) $this->respNOK('hasil tidak ditemukan');

        $this->respOK($lists);
    }
}
