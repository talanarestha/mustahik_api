<?php
class PengajuanSurvey extends Base 
{
    protected $tablename = 'pengajuan_survey';
    protected $keys = ['id'];

    public function getByPengajuan ($pengajuanId)
    {
        return $this->getRecordBy(["pengajuan_id='$pengajuanId'"], true);
    }
}