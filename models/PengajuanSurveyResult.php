<?php
class PengajuanSurveyResult extends Base
{
    protected $tablename = 'pengajuan_survey_result';
    protected $keys = ['id'];

    public function getByPengajuanSurvey ($pengajuanSurveyId, $surveyId)
    {
        return $this->getRecordBy(["pengajuan_survey_id='$pengajuanSurveyId'", "survey_id='$surveyId'"], true);
    }

    public function updateAnswer ($id, $optionId)
    {
        return $this->updateRecord(
            (object)[
                'id'        => $id,
                'option_id' => $optionId
            ]
        );
    }
}