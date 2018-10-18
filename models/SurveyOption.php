<?php
class SurveyOption extends Base 
{
    protected $tablename = 'survey_option';
    protected $keys = ['id'];

    public function getPilihan ($surveyId)
    {
        $result = $this->db->fetchAll(
            "SELECT * FROM {$this->tablename} WHERE survey_id=? ORDER BY sort", 
            Phalcon\Db::FETCH_ASSOC, array($surveyId)
        );
        return $result?:[];
    }
}