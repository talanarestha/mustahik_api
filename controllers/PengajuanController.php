<?php

class PengajuanController extends ControllerBase
{
    public function index() 
    {
        $user_id =  $this->request->getPost('tipe',['int'], 0);
        $type = $this->request->getPost('tipe',['int'], 0);
        $start = $this->request->getPost('start',['int'], 0);
        $offset = $this->request->getPost('offset',['int'], 15);

        $model = new Pengajuan;
        $data = [
            'pengajuan' => [],
            'row'       => 0,
            'offset'    => $offset
        ];

        $aCondition = [ 'pengajuan.survey=1' ];
        if ($type == 2) 
        {
            $aCondition[] = "pengajuan.status=1";
            $aCondition[] = "pengajuan_survey.surveyor='$user_id'";
        }
        else if ($type == 1) 
        {
            $aCondition[] = "pengajuan.status=0";
            $aCondition[] = "pengajuan_survey.surveyor='$user_id'";
        }
        else $aCondition[] = "pengajuan.status=0";

        if ($records = $model->getListBy ($aCondition, $start, $offset))        
        {
            foreach ($records as $record)
            {
                $data['pengajuan'][] = $this->mustahik_helper->normalizePengajuan($record);
            }

            $data['row'] = $offset + $start;
        }

        $this->respOK($data);
    }

    public function get() 
    {
        $id =  $this->request->getPost('pengajuan_id', ['string'], '0');
        $model = new Pengajuan;

        if ($record = $model->getById($id))
        {
            $record = $this->mustahik_helper->normalizePengajuan($record);
            $this->respOK($record);
        }

        $this->respNOK(-1, "Data tidak ditemukan");
    }

    public function search() 
    {
        $query = $this->request->getPost('query',['string'], "-");
        $start = $this->request->getPost('start',['int'], 0);
        $offset = $this->request->getPost('offset',['int'], 15);

        $model = new Pengajuan;
        $data = [
            'result' => [],
            'row'       => 0,
            'offset'    => $offset
        ];

        $aCondition = [ 'pengajuan.survey=1', "pengajuan.pemohon like '%$query%'" ];

        if ($records = $model->getListBy ($aCondition, $start, $offset))        
        {
            foreach ($records as $record)
            {
                $data['result'][] = $this->mustahik_helper->normalizePengajuan($record);
            }

            $data['row'] = $offset + $start;
        }

        $this->respOK($data);
    }

}