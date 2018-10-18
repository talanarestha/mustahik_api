<?php
class Survey extends Base 
{
    protected $tablename = 'survey';
    protected $keys = ['id'];

    public function getById($id) 
    {
        $result = $this->db->fetchOne(
            "SELECT s.*, k.nama_kategori, k.sort as kategori_sort
            FROM {$this->tablename} s 
            LEFT JOIN survey_kategori k ON k.id=s.kategori_id
            WHERE s.id=?", 
            Phalcon\Db::FETCH_ASSOC, array($id)
        );
        
        if ( !$result ) {
            return false;
        }
        
        return $result;
    }

    public function getLastSortIndex ($kategori_id)
    {
        $result = $this->db->fetchOne(
            "SELECT MAX(sort) as last_sort
            FROM {$this->tablename} s 
            WHERE kategori_id=?", 
            Phalcon\Db::FETCH_ASSOC, array($kategori_id)
        );
        return $result ? $result['last_sort'] : 0;
    }

    public function getDefaultSurvey ()
    {
        $result = $this->db->fetchAll(
            "SELECT s.*, k.nama_kategori, k.sort as kategori_sort, p.sort as pilihan_sort, p.nama_pilihan, p.bobot, p.id as pilihan_id, 0 as jawab_id
            FROM {$this->tablename} s 
            LEFT JOIN survey_kategori k ON k.id=s.kategori_id
            LEFT JOIN survey_option p ON p.survey_id=s.id
            WHERE s.status=1
            ORDER BY k.sort, s.sort, p.sort", 
            Phalcon\Db::FETCH_ASSOC
        );

        return $result?:[];
    }

    public function getSurveyByPengajuan ($pengajuanId)
    {
        $result = $this->db->fetchAll(
            "SELECT s.*, k.nama_kategori, k.sort as kategori_sort, p.sort as pilihan_sort, p.nama_pilihan, p.bobot, p.id as pilihan_id, r.option_id as jawab_id
            FROM pengajuan_survey_result r
            LEFT JOIN pengajuan_survey ps ON ps.id=r.pengajuan_survey_id
            LEFT JOIN {$this->tablename} s ON s.id=r.survey_id
            LEFT JOIN survey_kategori k ON k.id=s.kategori_id
            LEFT JOIN survey_option p ON p.survey_id=s.id
            WHERE s.status=1 AND ps.pengajuan_id=?
            ORDER BY k.sort, s.sort, p.sort", 
            Phalcon\Db::FETCH_ASSOC, [$pengajuanId]
        );

        return $result?:[];
    }

    public function getSurveyResult ($pengajuanId)
    {
        $result = $this->db->fetchAll(
            "SELECT s.*, k.nama_kategori, k.sort as kategori_sort, p.sort as pilihan_sort, p.nama_pilihan, p.bobot, p.id as pilihan_id, r.option_id as jawab_id
            FROM pengajuan_survey_result r
            LEFT JOIN pengajuan_survey ps ON ps.id=r.pengajuan_survey_id
            LEFT JOIN {$this->tablename} s ON s.id=r.survey_id
            LEFT JOIN survey_kategori k ON k.id=s.kategori_id
            LEFT JOIN survey_option p ON p.survey_id=r.survey_id and r.option_id=p.id
            WHERE s.status=1 AND ps.pengajuan_id=?
            ORDER BY k.sort, s.sort, p.sort", 
            Phalcon\Db::FETCH_ASSOC, [$pengajuanId]
        );

        return $result?:[];
    }

}