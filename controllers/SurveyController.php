<?php

class SurveyController extends ControllerBase
{
    public function get ()
    {
        $id = $this->request->getPost('pengajuan_id',['string'], "-1");

        $editable = false;
        $model = new Pengajuan;
        if ($data = $model->getById($id))
        {
            if ($data['survey'] == 1)
            {
                $mPengajuanSurvey = new PengajuanSurvey;

                $info_survey = $mPengajuanSurvey->getByPengajuan($id);

                $modelSurvey = new Survey;
                $list_survey = [];
                $list_kategori = [];
                $nilai_skor = 0;
                if ($aSurvey = $info_survey ? $modelSurvey->getSurveyByPengajuan ($id) : $modelSurvey->getDefaultSurvey ())
                {
                    $_survey_id=0;
                    $_kategori = '';
                    $_survey = [];
                    $_no = 1;
                    $_index = -1;
                    $_indexS = -1;
                    foreach ($aSurvey as $survey)
                    {
                        if ($_kategori != $survey['kategori_id'])
                        {
                            $_kategori = $survey['kategori_id'];
                            $list_kategori[] = $survey['nama_kategori'];
                            $_index++;
                            $_indexS = -1;
                        }

                        if ($_survey_id != $survey['id'])
                        {
                            if ($_survey)
                            {
                                $list_survey[$_index][] = $_survey;
                            }


                            $idx = 0;
                            $_survey = [
                                'id'        => $survey['id'],
                                'no'        => $_no,
                                'subject'   => $survey['subject'],
                                'jawaban'   => $survey['jawab_id']?:0,
                                'option'    => [
                                    [
                                        'id'            => $survey['pilihan_id'],
                                        'code'          => chr(97+$idx),
                                        'nama_pilihan'  => $survey['nama_pilihan'],
                                        'bobot'         => $survey['bobot'],
                                        'jawaban'       => $survey['jawab_id'] == $survey['pilihan_id'],
                                    ]
                                ]
                            ];

                            if ($survey['jawab_id'] == $survey['pilihan_id'])
                                $nilai_skor += $survey['bobot'];

                            $_survey_id = $survey['id'];
                            //$_kategori = $survey['nama_kategori'];
                            $_no++;
                            $_indexS++;
                        }
                        else
                        {
                            $idx++;
                            $_survey['option'][] = [
                                'id'            => $survey['pilihan_id'],
                                'code'          => chr(97+$idx),
                                'nama_pilihan'  => $survey['nama_pilihan'],
                                'bobot'         => $survey['bobot'],
                                'jawaban'       => $survey['jawab_id'] == $survey['pilihan_id'],
                            ];

                            if ($survey['jawab_id'] == $survey['pilihan_id'])
                                $nilai_skor += $survey['bobot'];

                        }
                    }
                    $list_survey[$_index][] = $_survey;
                }

                if (empty($info_survey))
                {
                    $info_survey = [
                        'tanggal_survey'    => date('Y-m-d'),
                        'waktu_survey'      => '',
                        'kelayakan'         => '',
                        'catatan'           => '',
                        'rekomendasi_lamusta'=> '',
                        'status_rekomendasi'=> ''
                    ];
                }

                $aData = [
                    'info'      => $this->mustahik_helper->normalizePengajuanSurvey($info_survey),
                    'survey'    => $list_survey,
                    'skor'      => $nilai_skor,
                    'kategori'  => $list_kategori
                ];

                $this->respOK($aData);
            }
        }

        $this->respNOK(-1, "Data tidak ditemukan");
    }

    public function save()
    {
        $id =  $this->request->getPost('pengajuan_id', ['string'], '0');
        $jawaban =  $this->request->getPost('jawaban');
        $surveyor =  $this->request->getPost('user_id');
        $model = new Pengajuan;

        if ($record = $model->getById($id))
        {
            $mPengajuanSurvey = new PengajuanSurvey;
            if ($record['status'] == 0 && $record['survey'] == 1)
            {
                $mSurveyResult = new PengajuanSurveyResult;

                $dataSurvey = new stdClass;
                $dataSurvey->tanggal_survey = date('Y-m-d');
                $dataSurvey->surveyor = $surveyor;

                $act = new Activities;
                $act->setUserId($surveyor);

                $aSurveyAnswer = json_decode($jawaban);

                if (empty($aSurveyAnswer))
                    $this->respNOK(-1, "Data Survey tidak lengkap");

                if ($info_survey = $mPengajuanSurvey->getByPengajuan($id))
                {
                    $dataSurvey->id = $info_survey['id'];
                    $answered = 0;
                    if ($mPengajuanSurvey->updateRecord($dataSurvey))
                    {
                        $pengajuanSurveyId = $dataSurvey->id;
                        foreach ($aSurveyAnswer as $qId  => $aId)
                        {
                            if ($aId != 0) $answered ++;

                            if ($resultSurvey = $mSurveyResult->getByPengajuanSurvey($pengajuanSurveyId, $qId))
                            {
                                /* $mSurveyResult->updateRecordBy(
                                    (object)[
                                        'option_id'             => $aId
                                    ],[
                                        "pengajuan_survey_id='$pengajuanSurveyId'",
                                        "survey_id='$qId'"]
                                ); */
                                $mSurveyResult->updateAnswer( $resultSurvey['id'], $aId);
                            }
                            else
                            {
                                $mSurveyResult->addRecord((object)[
                                    'pengajuan_survey_id'   => $pengajuanSurveyId,
                                    'survey_id'             => $qId,
                                    'option_id'             => $aId
                                ]);
                            }
                        }

                        $mPengajuanSurvey->setStatus($aSurveyAnswer, $answered == count($aQuestionId) ? 1 : 0);

                        $act->log('update verifikasi survey pengajuan','success', '');
                        return $this->respOK();
                    }

                }
                else
                {
                    $dataSurvey->created = date('Y-m-d H:i:s');
                    $dataSurvey->pengajuan_id = $id;

                    if ($mPengajuanSurvey->addRecord($dataSurvey))
                    {
                        $pengajuanSurveyId = $mPengajuanSurvey->getInsertId();
                        $answered = 0;
                        foreach ($aSurveyAnswer as $qId  => $aId)
                        {
                            if ($aId != 0) $answered ++;

                            $mSurveyResult->addRecord((object)[
                                'pengajuan_survey_id'   => $pengajuanSurveyId,
                                'survey_id'             => $qId,
                                'option_id'             => $aId
                            ]);
                        }

                        $mPengajuanSurvey->setStatus($aSurveyAnswer, $answered == count($aQuestionId) ? 1 : 0);
                        $act->log('set verifikasi survey pengajuan','success', '');
                        return $this->respOK();
                    }
                }

                $this->respNOK(-1, "Internal Error. Hubungi Administrator untuk informasi lebih lanjut");
            }
            $this->respNOK(-1, "Data Pengajuan sudah ditutup atau Data Survey tidak diperlukan");
        }

        $this->respNOK(-1, "Data tidak ditemukan");
    }
}