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
}
