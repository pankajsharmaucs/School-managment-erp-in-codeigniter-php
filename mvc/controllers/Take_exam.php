<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once(APPPATH . 'libraries/PaymentGateway/PaymentGateway.php');

class Take_exam extends Admin_Controller
{
    /*
    | -----------------------------------------------------
    | PRODUCT NAME: 	INILABS SCHOOL MANAGEMENT SYSTEM
    | -----------------------------------------------------
    | AUTHOR:			INILABS TEAM
    | -----------------------------------------------------
    | EMAIL:			info@inilabs.net
    | -----------------------------------------------------
    | COPYRIGHT:		RESERVED BY INILABS IT
    | -----------------------------------------------------
    | WEBSITE:			http://inilabs.net
    | -----------------------------------------------------
    */

    public $payment_gateway;
    public $payment_gateway_array;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('online_exam_m');
        $this->load->model('online_exam_payment_m');
        $this->load->model('online_exam_question_m');
        $this->load->model('instruction_m');
        $this->load->model('question_bank_m');
        $this->load->model('question_option_m');
        $this->load->model('question_answer_m');
        $this->load->model('online_exam_user_answer_m');
        $this->load->model('online_exam_user_status_m');
        $this->load->model('online_exam_user_answer_option_m');
        $this->load->model('student_m');
        $this->load->model('classes_m');
        $this->load->model('student_m');
        $this->load->model('section_m');
        $this->load->model('subject_m');
        $this->load->model('payment_gateway_m');
        $this->load->model('payment_gateway_option_m');
        $language = $this->session->userdata('lang');
        $this->lang->load('take_exam', $language);

        $this->payment_gateway       = new PaymentGateway();
        $this->payment_gateway_array = pluck($this->payment_gateway_m->get_order_by_payment_gateway(['status' => 1]), 'status', 'slug');
    }

    public function index()
    {
        $this->data['headerassets'] = [
            'css' => [
                'assets/select2/css/select2.css',
                'assets/select2/css/select2-bootstrap.css'
            ],
            'js'  => [
                'assets/select2/select2.js'
            ]
        ];

        $usertypeID  = $this->session->userdata('usertypeID');
        $loginuserID = $this->session->userdata('loginuserID');

        $this->data['userSubjectPluck'] = [];
        if ($usertypeID == '3') {
            $this->data['student'] = $this->student_m->get_single_student(['studentID' => $loginuserID]);
            if (inicompute($this->data['student'])) {
                $this->data['userSubjectPluck'] = pluck($this->subject_m->get_order_by_subject([
                    'classesID' => $this->data['student']->classesID,
                    'type'      => 1
                ]), 'subjectID', 'subjectID');
                $optionalSubject                = $this->subject_m->get_single_subject([
                    'type'      => 0,
                    'subjectID' => $this->data['student']->optionalsubjectID
                ]);
                if (inicompute($optionalSubject)) {
                    $this->data['userSubjectPluck'][$optionalSubject->subjectID] = $optionalSubject->subjectID;
                }
            }
        }

        $this->data['payment_settings'] = $this->payment_gateway_m->get_order_by_payment_gateway(['status' => 1]);
        $this->data['payment_options']  = pluck($this->payment_gateway_option_m->get_payment_gateway_option(), 'payment_value', 'payment_option');

        $this->data['payments']         = pluck_multi_array($this->online_exam_payment_m->get_order_by_online_exam_payment([
            'usertypeID' => $this->session->userdata('usertypeID'),
            'userID'     => $this->session->userdata('loginuserID')
        ]), 'obj', 'online_examID');
        $this->data['paindingpayments'] = pluck($this->online_exam_payment_m->get_order_by_online_exam_payment([
            'usertypeID' => $this->session->userdata('usertypeID'),
            'userID'     => $this->session->userdata('loginuserID'),
            'status'     => 0
        ]), 'obj', 'online_examID');
        $this->data['examStatus']       = pluck($this->online_exam_user_status_m->get_order_by_online_exam_user_status(['userID' => $loginuserID]), 'obj', 'onlineExamID');
        $this->data['usertypeID']       = $usertypeID;
        $this->data['onlineExams']      = $this->online_exam_m->get_order_by_online_exam([
            'usertypeID' => $usertypeID,
            'published'  => 1
        ]);

        $this->data['validationErrors']       = [];
        $this->data['validationOnlineExamID'] = 0;
        if ($_POST) {
            $rules = $this->payment_rules($this->input->post('payment_method'));
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == FALSE) {
                $this->data['validationOnlineExamID'] = $this->input->post('onlineExamID');
                $this->data['validationErrors']       = $this->form_validation->error_array();
                $this->data["subview"]                = "online_exam/take_exam/index";
                $this->load->view('_layout_main', $this->data);
            } else {
                if ($this->input->post('onlineExamID')) {
                    $invoice_data = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $this->input->post('onlineExamID')]);

                    if (($invoice_data->paid == 1) && ((float)$invoice_data->cost == 0)) {
                        $this->session->set_flashdata('error', 'Exam amount can not be zero');
                        redirect(base_url('take_exam/index'));
                    }

                    if (($invoice_data->examStatus == 1) && ($invoice_data->paid == 1) && isset($this->data['paindingpayments'][$invoice_data->onlineExamID])) {
                        $this->session->set_flashdata('error', 'This exam price already paid');
                        redirect(base_url('take_exam/index'));
                    }

                    $this->payment_gateway->gateway($this->input->post('payment_method'))->payment($this->input->post(), $invoice_data);
                } else {
                    $this->session->set_flashdata('error', 'Exam does not found');
                    redirect(base_url('take_exam/index'));
                }
            }
        } else {
            $this->data["subview"] = "online_exam/take_exam/index";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function show() //done
    {
        $this->data['headerassets'] = [
            'css' => [
                'assets/checkbox/checkbox.css',
                'assets/inilabs/form/fuelux.min.css'
            ]
        ];
        $this->data['footerassets'] = [
            'js' => [
                'assets/inilabs/form/fuelux.min.js'
            ]
        ];

        $userID       = $this->session->userdata("loginuserID");
        $onlineExamID = htmlentities(escapeString($this->uri->segment(3)));

        $examGivenStatus     = FALSE;
        $examGivenDataStatus = FALSE;
        $examExpireStatus    = FALSE;
        $examSubjectStatus   = FALSE;

        if ((int)$onlineExamID) {

            $this->data['student'] = $this->student_m->get_student($userID);
            if (inicompute($this->data['student'])) {
                $array['classesID']      = $this->data['student']->classesID;
                $array['sectionID']      = $this->data['student']->sectionID;
                $array['studentgroupID'] = $this->data['student']->studentgroupID;
                $array['onlineExamID']   = $onlineExamID;
                $online_exam             = $this->online_exam_m->get_online_exam_by_student($array);


                $userExamCheck = $this->online_exam_user_status_m->get_order_by_online_exam_user_status([
                    'userID'       => $userID,
                    'classesID'    => $array['classesID'],
                    'sectionID'    => $array['sectionID'],
                    'onlineExamID' => $onlineExamID
                ]);

                if (inicompute($online_exam)) {
                    $DDonlineExam = $online_exam;

                    if ($DDonlineExam->examTypeNumber == '4') {
                        $presentDate   = strtotime(date('Y-m-d'));
                        $examStartDate = strtotime($DDonlineExam->startDateTime);
                        $examEndDate   = strtotime($DDonlineExam->endDateTime);
                    } elseif ($DDonlineExam->examTypeNumber == '5') {
                        $presentDate   = strtotime(date('Y-m-d H:i:s'));
                        $examStartDate = strtotime($DDonlineExam->startDateTime);
                        $examEndDate   = strtotime($DDonlineExam->endDateTime);
                    }

                    if ($DDonlineExam->examTypeNumber == '4' || $DDonlineExam->examTypeNumber == '5') {
                        if ($presentDate >= $examStartDate && $presentDate <= $examEndDate) {
                            $examGivenStatus = TRUE;
                        } elseif ($presentDate > $examStartDate && $presentDate > $examEndDate) {
                            $examExpireStatus = TRUE;
                        }
                    } else {
                        $examGivenStatus = TRUE;
                    }

                    if ($examGivenStatus) {
                        $examGivenStatus = FALSE;
                        if ($DDonlineExam->examStatus == 2) {
                            $examGivenStatus = TRUE;
                        } else {
                            $userExamCheck = pluck($userExamCheck, 'obj', 'onlineExamID');
                            if (isset($userExamCheck[$DDonlineExam->onlineExamID])) {
                                $examGivenDataStatus = TRUE;
                            } else {
                                $examGivenStatus = TRUE;
                            }
                        }
                    }

                    if ($examGivenStatus) {
                        if ((int)$DDonlineExam->subjectID && (int)$DDonlineExam->classID) {
                            $examGivenStatus  = FALSE;
                            $userSubjectPluck = pluck($this->subject_m->get_order_by_subject(['type' => 1]), 'subjectID', 'subjectID');
                            $optionalSubject  = $this->subject_m->get_single_subject([
                                'type'      => 0,
                                'subjectID' => $this->data['student']->optionalsubjectID
                            ]);
                            if (inicompute($optionalSubject)) {
                                $userSubjectPluck[$optionalSubject->subjectID] = $optionalSubject->subjectID;
                            }

                            if (in_array($DDonlineExam->subjectID, $userSubjectPluck)) {
                                $examGivenStatus = TRUE;
                            } else {
                                $examSubjectStatus = FALSE;
                            }
                        } else {
                            $examSubjectStatus = TRUE;
                        }
                    } else {
                        $examSubjectStatus = TRUE;
                    }
                }

                $this->data['class'] = $this->classes_m->get_classes($this->data['student']->classesID);
            } else {
                $this->data['class'] = [];
            }

            if (inicompute($this->data['student'])) {
                $this->data['section'] = $this->section_m->get_section($this->data['student']->sectionID);
            } else {
                $this->data['section'] = [];
            }

            $this->data['onlineExam'] = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $onlineExamID]);
            if (inicompute($online_exam)) {
                $onlineExamQuestions = $this->online_exam_question_m->get_order_by_online_exam_question(['onlineExamID' => $onlineExamID]);

                $allOnlineExamQuestions = $onlineExamQuestions;

                if ($this->data['onlineExam']->random == 1) {
                    $this->db->from('online_exam_question')->where(['onlineExamID' => $onlineExamID])->order_by('', RANDOM);
                    $query = $this->db->get();
                    $onlineExamQuestions = $query->result();
                    $allOnlineExamQuestions = $onlineExamQuestions;
                }

                $this->data['onlineExamQuestions'] = $onlineExamQuestions;
                $onlineExamQuestions               = pluck($onlineExamQuestions, 'obj', 'questionID');
                $questionsBank                     = pluck($this->question_bank_m->get_order_by_question_bank(), 'obj', 'questionBankID');
                $this->data['questions']           = $questionsBank;


                $options    = [];
                $answers    = [];
                $allOptions = [];
                $allAnswers = [];
                if (inicompute($allOnlineExamQuestions)) {
                    $pluckOnlineExamQuestions = pluck($allOnlineExamQuestions, 'questionID');
                    $allOptions               = $this->question_option_m->get_where_in_question_option($pluckOnlineExamQuestions, 'questionID');
                    foreach ($allOptions as $option) {
                        if ($option->name == "" && $option->img == "")
                            continue;
                        $options[$option->questionID][] = $option;
                    }
                    $this->data['options'] = $options;

                    $allAnswers = $this->question_answer_m->get_where_in_question_answer($pluckOnlineExamQuestions, 'questionID');
                    foreach ($allAnswers as $answer) {
                        $answers[$answer->questionID][] = $answer;
                    }
                    $this->data['answers'] = $answers;
                } else {
                    $this->data['options'] = $options;
                    $this->data['answers'] = $answers;
                }
                if ($_POST) {

                    $time               = date("Y-m-d h:i:s");
                    $mainQuestionAnswer = [];
                    $userAnswer         = $this->input->post('answer');


                    foreach ($allAnswers as $answer) {
                        if ($answer->typeNumber == 3) {
                            $mainQuestionAnswer[$answer->typeNumber][$answer->questionID][$answer->answerID] = $answer->text;
                        } else {
                            $mainQuestionAnswer[$answer->typeNumber][$answer->questionID][] = $answer->optionID;
                        }
                    }

                    $questionStatus    = [];
                    $correctAnswer     = 0;
                    $totalQuestionMark = 0;
                    $totalCorrectMark  = 0;
                    $visited           = [];

                    $totalAnswer = 0;
                    if (inicompute($userAnswer)) {
                        foreach ($userAnswer as $userAnswerKey => $uA) {
                            $totalAnswer += inicompute($uA);
                        }
                    }


                    if (inicompute($allOnlineExamQuestions)) {
                        foreach ($allOnlineExamQuestions as $aoeq) {
                            if (isset($questionsBank[$aoeq->questionID])) {
                                $totalQuestionMark += $questionsBank[$aoeq->questionID]->mark;
                            }
                        }
                    }

                    $f        = 0;

                    $examtime =  $this->db->select('examtimeID')->from('online_exam_user_status')
                        ->where([
                            'userID'       => $userID,
                            'onlineExamID' => $onlineExamID
                        ])
                        ->limit('1')
                        ->order_by('onlineExamUserStatus', 'DESC')
                        ->get()->row();

                    $examTimeCounter = 1;
                    if (inicompute($examtime)) {
                        $examTimeCounter = $examtime->examtimeID;
                        $examTimeCounter++;
                    }
                    $this->data['attemptedID']     = $examTimeCounter;

                    $statusID = 10;
                    foreach ($mainQuestionAnswer as $typeID => $questions) {
                        if (!isset($userAnswer[$typeID]))
                            continue;
                        foreach ($questions as $questionID => $options) {
                            if (isset($onlineExamQuestions[$questionID])) {
                                $onlineExamQuestionID   = $onlineExamQuestions[$questionID]->onlineExamQuestionID;
                                $onlineExamUserAnswerID = $this->online_exam_user_answer_m->insert([
                                    'onlineExamQuestionID' => $onlineExamQuestionID,
                                    'userID'               => $userID,
                                    'onlineExamID'         => $onlineExamID,
                                    'examtimeID'           => $examTimeCounter
                                ]);
                            }

                            if (isset($userAnswer[$typeID][$questionID])) {
                                $totalCorrectMark += isset($questionsBank[$questionID]) ? $questionsBank[$questionID]->mark : 0;

                                $questionStatus[$questionID] = 1;
                                $correctAnswer++;
                                $f = 1;
                                if ($typeID == 3) {
                                    foreach ($options as $answerID => $answer) {
                                        $takeAnswer = strtolower($answer);
                                        $getAnswer  = isset($userAnswer[$typeID][$questionID][$answerID]) ? strtolower($userAnswer[$typeID][$questionID][$answerID]) : '';
                                        $this->online_exam_user_answer_option_m->insert([
                                            'questionID'   => $questionID,
                                            'typeID'       => $typeID,
                                            'text'         => $getAnswer,
                                            'time'         => $time,
                                            'onlineExamID' => $onlineExamID,
                                            'examtimeID'   => $examTimeCounter,
                                            'userID'       => $userID,
                                        ]);
                                        if ($getAnswer != $takeAnswer) {
                                            $f = 0;
                                        }
                                    }
                                } elseif ($typeID == 1 || $typeID == 2) {
                                    if (inicompute($options) != inicompute($userAnswer[$typeID][$questionID])) {
                                        $f = 0;
                                    } else {
                                        if (!isset($visited[$typeID][$questionID])) {
                                            foreach ($userAnswer[$typeID][$questionID] as $userOption) {
                                                $this->online_exam_user_answer_option_m->insert([
                                                    'questionID'   => $questionID,
                                                    'optionID'     => $userOption,
                                                    'typeID'       => $typeID,
                                                    'time'         => $time,
                                                    'onlineExamID' => $onlineExamID,
                                                    'examtimeID'   => $examTimeCounter,
                                                    'userID'       => $userID,
                                                ]);
                                            }
                                            $visited[$typeID][$questionID] = 1;
                                        }
                                        foreach ($options as $answerID => $answer) {
                                            if (!in_array($answer, $userAnswer[$typeID][$questionID])) {
                                                $f = 0;
                                                break;
                                            }
                                        }
                                    }
                                }

                                if (!$f) {
                                    $questionStatus[$questionID] = 0;
                                    $correctAnswer--;
                                    $totalCorrectMark -= $questionsBank[$questionID]->mark;
                                }
                            }
                        }
                    }
                    if (inicompute($this->data['onlineExam'])) {
                        if ($this->data['onlineExam']->markType == 5) {

                            $percentage = 0;
                            if ($totalCorrectMark > 0 && $totalQuestionMark > 0) {
                                $percentage = (($totalCorrectMark / $totalQuestionMark) * 100);
                            }

                            if ($percentage >= $this->data['onlineExam']->percentage) {
                                $statusID = 5;
                            } else {
                                $statusID = 10;
                            }
                        } elseif ($this->data['onlineExam']->markType == 10) {
                            if ($totalCorrectMark >= $this->data['onlineExam']->percentage) {
                                $statusID = 5;
                            } else {
                                $statusID = 10;
                            }
                        }
                    }

                    $this->online_exam_user_status_m->insert([
                        'onlineExamID'       => $this->data['onlineExam']->onlineExamID,
                        'time'               => $time,
                        'totalQuestion'      => inicompute($onlineExamQuestions),
                        'totalAnswer'        => $totalAnswer,
                        'nagetiveMark'       => $this->data['onlineExam']->negativeMark,
                        'duration'           => $this->data['onlineExam']->duration,
                        'score'              => $correctAnswer,
                        'userID'             => $userID,
                        'classesID'          => inicompute($this->data['class']) ? $this->data['class']->classesID : 0,
                        'sectionID'          => inicompute($this->data['section']) ? $this->data['section']->sectionID : 0,
                        'examtimeID'         => $examTimeCounter,
                        'totalCurrectAnswer' => $correctAnswer,
                        'totalMark'          => $totalQuestionMark,
                        'totalObtainedMark'  => $totalCorrectMark,
                        'totalPercentage'    => (($totalCorrectMark > 0 && $totalQuestionMark > 0) ? (($totalCorrectMark / $totalQuestionMark) * 100) : 0),
                        'statusID'           => $statusID,
                    ]);

                    if ($this->data['onlineExam']->paid) {
                        $onlineExamPayments = $this->online_exam_payment_m->get_single_online_exam_payment_only_first_row([
                            'online_examID' => $this->data['onlineExam']->onlineExamID,
                            'status'        => 0,
                            'usertypeID'    => $this->session->userdata('usertypeID'),
                            'userID'        => $this->session->userdata('loginuserID')
                        ]);

                        if ($onlineExamPayments->online_exam_paymentID != NULL) {
                            $onlineExamPaymentArray = [
                                'status' => 1
                            ];
                            $this->online_exam_payment_m->update_online_exam_payment($onlineExamPaymentArray, $onlineExamPayments->online_exam_paymentID);
                        }
                    }

                    $allUserExams = $this->online_exam_user_status_m->get_online_exam_user_status();
                    $givenTimes = [];
                    $allExams = pluck($this->online_exam_m->get_online_exam(), 'showMarkAfterExam', 'onlineExamID');
                    foreach ($allUserExams as $allUserExam) {
                        if (!array_key_exists($allUserExam->onlineExamID, $givenTimes)) {
                            $givenTimes[$allUserExam->onlineExamID] = $allExams[$allUserExam->onlineExamID];
                        }
                    }

                    $this->data['showResult']        = $givenTimes;
                    $this->data['fail']              = $f;
                    $this->data['questionStatus']    = $questionStatus;
                    $this->data['totalAnswer']       = $totalAnswer;
                    $this->data['correctAnswer']     = $correctAnswer;
                    $this->data['totalCorrectMark']  = $totalCorrectMark;
                    $this->data['totalQuestionMark'] = $totalQuestionMark;
                    $this->data['userExamCheck']     = $userExamCheck;
                    $this->data['onlineExamID']      = $onlineExamID;
                    $this->data["subview"]           = "online_exam/take_exam/result";
                    return $this->load->view('_layout_main', $this->data);
                }
                if ($examGivenStatus) {
                    $this->data["subview"] = "online_exam/take_exam/question";
                    return $this->load->view('_layout_main', $this->data);
                } else {
                    if ($examGivenDataStatus) {
                        $this->data['online_exam']   = $online_exam;
                        $userExamCheck               = pluck($userExamCheck, 'obj', 'onlineExamID');
                        $this->data['userExamCheck'] = isset($userExamCheck[$onlineExamID]) ? $userExamCheck[$onlineExamID] : [];
                        $this->data["subview"]       = "online_exam/take_exam/checkexam";
                        return $this->load->view('_layout_main', $this->data);
                    } else {
                        if ($examExpireStatus) {
                            $this->data['examsubjectstatus'] = $examSubjectStatus;
                            $this->data['expirestatus']      = $examExpireStatus;
                            $this->data['upcomingstatus']    = FALSE;
                            $this->data['online_exam']       = $online_exam;
                            $this->data["subview"]           = "online_exam/take_exam/expireandupcoming";
                            return $this->load->view('_layout_main', $this->data);
                        } else {
                            $this->data['examsubjectstatus'] = $examSubjectStatus;
                            $this->data['expirestatus']      = $examExpireStatus;
                            $this->data['upcomingstatus']    = TRUE;
                            $this->data['online_exam']       = $online_exam;
                            $this->data["subview"]           = "online_exam/take_exam/expireandupcoming";
                            return $this->load->view('_layout_main', $this->data);
                        }
                    }
                }
            } else {
                $this->data["subview"] = "error";
                $this->load->view('_layout_main', $this->data);
            }
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function getAnswerList()
    {
        $onlineExamID = htmlentities(escapeString($this->uri->segment(3)));
        $attemptID = htmlentities(escapeString($this->uri->segment(4)));
        $studentID = htmlentities(escapeString($this->uri->segment(5)));

        $this->data['onlineExamID'] = $onlineExamID;
        $this->data['studentID'] = $studentID;
        $this->data['attemptID'] = $attemptID;
        $this->data['exam'] = $this->online_exam_m->get_single_online_exam(array('onlineExamID' => $onlineExamID));
        if (inicompute($this->data['exam'])) {
            $this->data['typeName'] = $this->lang->line('take_exam_question');
            $array = [];
            if ((int)$onlineExamID && $onlineExamID > 0) {
                $array['onlineExamID'] = $onlineExamID;
            }
            if ((int)$studentID && $studentID > 0) {
                $array['userID'] = $studentID;
            }
            if ((int)$attemptID && $attemptID > 0) {
                $array['examtimeID'] = $attemptID;
            }

            $examquestions = pluck($this->online_exam_question_m->get_order_by_online_exam_question(array('onlineExamID' => $onlineExamID)), 'questionID');
            $this->data['examquestionsuseranswer']  = pluck($this->online_exam_user_answer_option_m->get_order_by_online_exam_user_answer_option($array), 'obj', 'questionID');
            $this->data['examquestionsanswer'] = pluck($this->question_answer_m->get_question_answerArray($examquestions, 'questionID'), 'obj', 'questionID');
            $this->data['questions'] = pluck($this->question_bank_m->get_question_bank_questionArray($examquestions, 'questionBankID'), 'obj', 'questionBankID');
            $this->data['question_options'] = pluck_multi_array($this->question_option_m->get_question_option_by_questionArray($examquestions, 'questionID'), 'obj', 'questionID');
            $this->data["subview"] = "online_exam/take_exam/examanswer";
            $this->load->view('_layout_main', $this->data);
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function instruction() //done
    {
        $onlineExamID = htmlentities(escapeString($this->uri->segment(3)));
        if ((int)$onlineExamID) {
            $instructions             = pluck($this->instruction_m->get_order_by_instruction(), 'obj', 'instructionID');
            $onlineExam               = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $onlineExamID]);
            $this->data['onlineExam'] = $onlineExam;
            if (!isset($instructions[$onlineExam->instructionID])) {
                redirect(base_url('take_exam/show/' . $onlineExamID));
            }
            $this->data['instruction'] = $instructions[$onlineExam->instructionID];
            $this->data["subview"]     = "online_exam/take_exam/instruction";
            return $this->load->view('_layout_main', $this->data);
        } else {
            $this->data["subview"] = "error";
            $this->load->view('_layout_main', $this->data);
        }
    }

    public function randAssociativeArray($array, $number = 0)
    {
        $returnArray = [];
        $countArray  = inicompute($array);
        if ($number > $countArray || $number == 0) {
            $number = $countArray;
        }

        if ($countArray == 1) {
            $randomKey[] = 0;
        } else {
            if (inicompute($array)) {
                $randomKey = array_rand($array, $number);
            } else {
                $randomKey = [];
            }
        }

        if (is_array($randomKey)) {
            shuffle($randomKey);
        }

        if (inicompute($randomKey)) {
            foreach ($randomKey as $key) {
                $returnArray[] = $array[$key];
            }
            return $returnArray;
        } else {
            return $array;
        }
    }

    public function get_payment_info() //done
    {
        $onlineExamID = $this->input->post('onlineExamID');

        $retArray['status']        = false;
        $retArray['payableamount'] = 0.00;
        if (permissionChecker('take_exam')) {
            if (!empty($onlineExamID) && (int)$onlineExamID && $onlineExamID > 0) {
                $onlineExam = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $onlineExamID]);
                if (inicompute($onlineExam)) {
                    $retArray['status']        = true;
                    $retArray['payableamount'] = sprintf("%.2f", $onlineExam->cost);
                }
            }
        }

        echo json_encode($retArray);
        exit;
    }

    public function payment_list() //done
    {
        $onlineExamID = $this->input->post('onlineExamID');
        if (!empty($onlineExamID) && (int)$onlineExamID && $onlineExamID > 0) {
            $onlineExam = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $onlineExamID]);
            if (inicompute($onlineExam)) {
                $onlineExamPayments = $this->online_exam_payment_m->get_order_by_online_exam_payment([
                    'online_examID' => $onlineExamID,
                    'usertypeID'    => $this->session->userdata('usertypeID'),
                    'userID'        => $this->session->userdata('loginuserID')
                ]);
                if (inicompute($onlineExamPayments)) {
                    $i = 1;
                    foreach ($onlineExamPayments as $onlineExamPayment) {
                        echo '<tr>';
                        echo '<td data-title="' . $this->lang->line('slno') . '">';
                        echo $i;
                        echo '</td>';

                        echo '<td data-title="' . $this->lang->line('take_exam_payment_date') . '">';
                        echo date('d M Y', strtotime($onlineExamPayment->paymentdate));
                        echo '</td>';

                        echo '<td data-title="' . $this->lang->line('take_exam_payment_method') . '">';
                        echo $onlineExamPayment->paymentmethod;
                        echo '</td>';

                        echo '<td data-title="' . $this->lang->line('take_exam_exam_status') . '">';
                        if ($onlineExamPayment->status) {
                            echo $this->lang->line('take_exam_complete');
                        } else {
                            echo $this->lang->line('take_exam_pending');
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                }
            }
        }
    }

    protected function payment_rules($method): array //done
    {
        return $this->payment_gateway->gateway($method)->payment_rules([
            [
                'field' => 'payment_method',
                'label' => $this->lang->line("take_exam_payment_method"),
                'rules' => 'trim|required|xss_clean|max_length[11]|callback_unique_payment_method'
            ],
            [
                'field' => 'paymentAmount',
                'label' => $this->lang->line("take_exam_payment_amount"),
                'rules' => 'trim|required|xss_clean|max_length[16]'
            ]
        ]);
    }

    public function unique_payment_method(): bool  //done
    {
        if ($this->input->post('payment_method') === 'select') {
            $this->form_validation->set_message("unique_payment_method", "Payment method is required.");
            return false;
        } else {
            if (!$this->payment_gateway->gateway($this->input->post('payment_method'))->status()) {
                $this->form_validation->set_message("unique_payment_method", "The Payment method is disable now, try other payment method system");
                return false;
            }
            return true;
        }
    }

    public function paymentChecking()  //done
    {
        $onlineExamID        = $this->input->post('onlineExamID');
        $status              = 'FALSE';
        $paymentExpireStatus = TRUE;
        if ($onlineExamID > 0) {
            $onlineExam = $this->online_exam_m->get_single_online_exam(['onlineExamID' => $onlineExamID]);
            if (inicompute($onlineExam)) {
                if (($onlineExam->examStatus == 2) && ($onlineExam->paid == 1)) {

                    if ($onlineExam->examTypeNumber == '4') {
                        $presentDate   = strtotime(date('Y-m-d'));
                        $examStartDate = strtotime($onlineExam->startDateTime);
                        $examEndDate   = strtotime($onlineExam->endDateTime);
                    } elseif ($onlineExam->examTypeNumber == '5') {
                        $presentDate   = strtotime(date('Y-m-d H:i:s'));
                        $examStartDate = strtotime($onlineExam->startDateTime);
                        $examEndDate   = strtotime($onlineExam->endDateTime);
                    }

                    if ($onlineExam->examTypeNumber == '4' || $onlineExam->examTypeNumber == '5') {
                        if ($presentDate > $examStartDate && $presentDate > $examEndDate) {
                            $paymentExpireStatus = FALSE;
                        }
                    }

                    if ($paymentExpireStatus) {
                        $onlineExamPayments = $this->online_exam_payment_m->get_single_online_exam_payment_only_first_row([
                            'online_examID' => $onlineExamID,
                            'status'        => 0,
                            'usertypeID'    => $this->session->userdata('usertypeID'),
                            'userID'        => $this->session->userdata('loginuserID')
                        ]);
                        if ($onlineExamPayments->online_exam_paymentID == NULL) {
                            $status = 'TRUE';
                        }
                    }
                }
            }
        }

        echo $status;
    }

    public function success()
    {
        if (isset($this->payment_gateway_array[htmlentities(escapeString($this->uri->segment(3)))])) {
            $this->payment_gateway->gateway(htmlentities(escapeString($this->uri->segment(3))))->success();
        }
    }

    public function cancel()
    {
        if (isset($this->payment_gateway_array[htmlentities(escapeString($this->uri->segment(3)))])) {
            $this->payment_gateway->gateway(htmlentities(escapeString($this->uri->segment(3))))->cancel();
        }
    }

    public function fail()
    {
        if (isset($this->payment_gateway_array[htmlentities(escapeString($this->uri->segment(3)))])) {
            $this->payment_gateway->gateway(htmlentities(escapeString($this->uri->segment(3))))->fail();
        }
    }
}

