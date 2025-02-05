<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Onlineexampaymentreport extends Admin_Controller
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
	function __construct()
	{
		parent::__construct();
		$this->load->model('student_m');
		$this->load->model('classes_m');
		$this->load->model('section_m');
		$this->load->model('subject_m');
		$this->load->model('online_exam_payment_m');
		$this->load->model('online_exam_m');
		$this->load->model('studentrelation_m');
		$this->load->model('online_exam_user_status_m');
		$language = $this->session->userdata('lang');
		$this->lang->load('paymentreport_lang', $language);
	}


	public function index()
	{
		$this->data['headerassets'] = array(
			'css' => array(
				'assets/select2/css/select2.css',
				'assets/select2/css/select2-bootstrap.css'
			),
			'js' => array(
				'assets/select2/select2.js'
			)
		);

		$this->data['onlineexams'] 	= $this->online_exam_m->get_online_exam();
		$this->data['classes'] 		= $this->classes_m->get_classes();
		$this->data["subview"] 		= "report/payment/PaymentReport";
		$this->load->view('_layout_main', $this->data);
	}

	protected function rules()
	{
		$rules = array(
			array(
				'field' => 'onlineexamID',
				'label' => $this->lang->line('onlineexamreport_onlineexam'),
				'rules' => 'trim|required|xss_clean|callback_unique_data'
			),
			array(
				'field' => 'classesID',
				'label' => $this->lang->line('onlineexamreport_classes'),
				'rules' => 'trim|required|xss_clean|numeric|callback_unique_data'
			),
			array(
				'field' => 'sectionID',
				'label' => $this->lang->line('onlineexamreport_section'),
				'rules' => 'trim|required|xss_clean|numeric'
			),
			array(
				'field' => 'studentID',
				'label' => $this->lang->line('onlineexamreport_student'),
				'rules' => 'trim|required|xss_clean|numeric'
			),
		);
		return $rules;
	}

	protected function send_pdf_to_mail_rules()
	{
		$rules = array(
			array(
				'field' => 'to',
				'label' => $this->lang->line('onlineexamreport_to'),
				'rules' => 'trim|required|xss_clean|valid_email'
			), array(
				'field' => 'subject',
				'label' => $this->lang->line('onlineexamreport_subject'),
				'rules' => 'trim|required|xss_clean'
			), array(
				'field' => 'message',
				'label' => $this->lang->line('onlineexamreport_message'),
				'rules' => 'trim|xss_clean'
			),
		);
		return $rules;
	}

	public function unique_data()
	{
		$onlineexamID = $this->input->post('onlineexamID');
		$classesID = $this->input->post('classesID');

		if ($onlineexamID === "0" && $classesID === '0') {
			$this->form_validation->set_message('unique_data', 'The %s field is required.');
			return FALSE;
		}
		return TRUE;
	}

	public function unique_status()
	{
		$statusID = $this->input->post('statusID');

		if ($statusID === "0") {
			$this->form_validation->set_message('unique_status', 'The %s field is required.');
			return FALSE;
		}
		return TRUE;
	}

	public function getSection()
	{
		$classesID = $this->input->post('classesID');
		if ((int)$classesID) {
			$allSection = $this->section_m->get_order_by_section(array('classesID' => $classesID));
			echo "<option value='0'>", $this->lang->line("onlineexamreport_please_select"), "</option>";
			foreach ($allSection as $value) {
				echo "<option value=\"$value->sectionID\">", $value->section, "</option>";
			}
		}
	}

	public function getStudent()
	{
		$classesID = $this->input->post('classesID');
		$sectionID = $this->input->post('sectionID');
		if ($classesID && ($sectionID >= 0)) {
			$arrayBind = [];

			if ((int)$classesID) {
				$arrayBind['classesID'] = $classesID;
			}

			if ((int)$sectionID) {
				$arrayBind['sectionID'] = $sectionID;
			}

			$allStudent = $this->student_m->get_order_by_student($arrayBind);
			echo "<option value='0'>", $this->lang->line("onlineexamreport_please_select"), "</option>";
			foreach ($allStudent as $value) {
				echo "<option value=\"$value->studentID\">", $value->name, "</option>";
			}
		} else {
			echo "<option value='0'>", $this->lang->line("onlineexamreport_please_select"), "</option>";
		}
	}

	public function getUserList()
	{
		$retArray['status'] = FALSE;
		$retArray['render'] = '';

		$onlineexamID 	= $this->input->post('onlineexamID');
		$classesID 		= $this->input->post('classesID');
		$sectionID 		= $this->input->post('sectionID');
		$studentID 		= $this->input->post('studentID');
		if ($_POST) {
			$rules = $this->rules();
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() == FALSE) {
				$retArray = $this->form_validation->error_array();
				echo json_encode($retArray);
				exit;
			} else {
				$queryArray        = [];
				$paymentQueryArray = [];
				$this->getArray($queryArray, $paymentQueryArray, $this->input->post());
				$this->data['paymentreports'] = $this->online_exam_payment_m->get_order_by_online_exam_payment($paymentQueryArray);
				$this->data['students']       = pluck($this->student_m->get_student(), 'obj', 'studentID');
				$this->data['sections']       = pluck($this->section_m->get_section(), 'obj', 'sectionID');
				$retArray['render']         = $this->load->view('report/payment/PaymentReportView', $this->data, true);
				$retArray['status']         = TRUE;
				echo json_encode($retArray);
				exit;
			}
		}
	}

	private function getArray(&$queryArray, &$paymentQueryArray, $post)
	{
		$onlineexamID 	= $post['onlineexamID'];
		$classesID 		= $post['classesID'];
		$sectionID 		= $post['sectionID'];
		$studentID 		= $post['studentID'];

		if (isset($post['onlineexamID']) && $post['onlineexamID'] != 0) {
			$onlineexam      					= $this->online_exam_m->get_single_online_exam(['onlineexamID' => $onlineexamID]);
			$this->data['onlineexam']    = $onlineexam->name;
			$queryArray['onlineexamID']  = $onlineexamID;
			$paymentQueryArray['online_examID'] = $onlineexamID;
		}

		if (isset($post['classesID']) && $post['classesID'] != 0) {
			$queryArray['classesID'] = $classesID;
			$this->data['classes'] = $this->classes_m->get_single_classes(array('classesID' => $classesID));
		} else {
			$this->data['classes'] = array();
		}

		if (isset($post['sectionID']) && ($post['sectionID'] != '' && $post['sectionID'] != 0)) {
			$queryArray['sectionID'] = $sectionID;
			$this->data['section'] = $this->section_m->get_single_section(array('sectionID' => $sectionID));
		} else {
			$this->data['section'] = array();
		}

		if (isset($post['studentID']) && $post['studentID'] != 0) {
			$queryArray['userID'] = $studentID;
			$paymentQueryArray['userID'] = $studentID;
		}

		$this->data['onlineexamID'] = $onlineexamID;
		$this->data['classesID']	= $classesID;
		$this->data['sectionID'] 	= $sectionID;
		$this->data['studentID']	= $studentID;
	}

	public function result()
	{
		if (permissionChecker('onlineexamreport')) {
			$onlineExamUserStatusID = htmlentities(escapeString($this->uri->segment(3)));
			if ((int) $onlineExamUserStatusID) {
				$onlineExamUserStatus = $this->online_exam_user_status_m->get_single_online_exam_user_status(array('onlineExamUserStatus' => $onlineExamUserStatusID));
				if (inicompute($onlineExamUserStatus)) {
					$this->data['onlineExamUserStatus'] = $onlineExamUserStatus;
					$this->data['onlineexam'] = $this->online_exam_m->get_single_online_exam(array('onlineExamID' => $onlineExamUserStatus->onlineExamID));
					$this->data['subject'] = $this->subject_m->get_single_subject(array('subjectID' => $this->data['onlineexam']->subjectID));

					$this->data['rank'] = $this->ranking($onlineExamUserStatus->onlineExamID, $onlineExamUserStatus->userID,  $onlineExamUserStatus->examtimeID);

					$this->data['user'] = $this->student_m->get_student($onlineExamUserStatus->userID);
					$this->data['classes'] = $this->classes_m->get_classes($this->data['user']->classesID);
					$this->data['section'] = $this->section_m->get_section($this->data['user']->sectionID);

					$this->data["subview"] = "report/onlineexam/onlineexamResult";
					$this->load->view('_layout_main', $this->data);
				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
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

	public function ranking($onlineexamID, $userID, $examtimeID)
	{
		$onlineExamUserStatus = $this->online_exam_user_status_m->get_order_by_online_exam_user_status(array('onlineExamID' => $onlineexamID));

		$returnArray = [];
		$i = 1;
		if (inicompute($onlineExamUserStatus)) {
			foreach ($onlineExamUserStatus as $key => $value) {
				$returnArray[$value->onlineExamID][$value->userID][$value->examtimeID] = array(
					'rank' => $i
				);
				$i++;
			}
		}

		$position = '';
		if (inicompute($returnArray)) {
			if (isset($returnArray[$onlineexamID][$userID][$examtimeID]['rank'])) {
				$position = $returnArray[$onlineexamID][$userID][$examtimeID]['rank'];
			}
		}

		return $position;
	}

	public function pdf()
	{
		$onlineexamID = htmlentities(escapeString($this->uri->segment(3)));
		$studentID    = htmlentities(escapeString($this->uri->segment(4)));
		$sectionID    = htmlentities(escapeString($this->uri->segment(5)));
		$classesID    = htmlentities(escapeString($this->uri->segment(5)));

		if ($onlineexamID > 0) {
			$queryArray['onlineexamID'] = $onlineexamID;
		}
		if ($studentID > 0) {
			$queryArray['studentID'] = $studentID;
		}
		if ($sectionID > 0) {
			$queryArray['sectionID'] = $sectionID;
		}
		if ($sectionID > 0) {
			$queryArray['classesID'] = $classesID;
		}
		if (permissionChecker('onlineexampaymentreport')) {
			if ((int) $onlineexamID && (int) $studentID) {
				$this->getData($queryArray);
				$paymentreports = $this->online_exam_payment_m->get_order_by_online_exam_payment(['online_examID' => $onlineexamID, 'userID' => $studentID]);
				if (inicompute($paymentreports)) {
					$this->data['students']       = pluck($this->student_m->get_student(), 'obj', 'studentID');
					$this->data['sections']       = pluck($this->section_m->get_section(), 'obj', 'sectionID');
					$this->data['paymentreports'] = $paymentreports;
					$this->reportPDF('onlineexamreport.css', $this->data, 'report/payment/PaymentResultPDF');

				} else {
					$this->data["subview"] = "error";
					$this->load->view('_layout_main', $this->data);
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

	public function send_pdf_to_mail()
	{
		$retArray['status'] = FALSE;
		$retArray['message'] = '';
		if (permissionChecker('onlineexampaymentreport')) {
			if ($_POST) {
				$to           = $this->input->post('to');
				$subject      = $this->input->post('subject');
				$message      = $this->input->post('message');
				$onlineexamID = $this->input->post('onlineexamID');
				$studentID    = $this->input->post('studentID');
			
				$rules        = $this->send_pdf_to_mail_rules();
				$this->form_validation->set_rules($rules);
				if ($this->form_validation->run() == FALSE) {
					$retArray[] = $this->form_validation->error_array();
					$retArray['status'] = FALSE;
					echo json_encode($retArray);
					exit;
				} else {
					$paymentreports = $this->online_exam_payment_m->get_order_by_online_exam_payment(['online_examID' => $onlineexamID, 'userID' => $studentID]);
					if (inicompute($paymentreports)) {
						$queryArray        = [];
						$paymentQueryArray = [];
						$this->getArray($queryArray, $paymentQueryArray, $this->input->post());
						$this->data['paymentreports'] = $paymentreports;
						$this->data['students']       = pluck($this->student_m->get_student(), 'obj', 'studentID');
						$this->data['sections']       = pluck($this->section_m->get_section(), 'obj', 'sectionID');
						$this->reportSendToMail('onlineexamreport.css', $this->data, 'report/payment/PaymentResultPDF', $to, $subject, $message);
						$retArray['status'] = TRUE;
						echo json_encode($retArray);
						exit;
					} else {
						$retArray['message'] = $this->lang->line("onlineexamreport_onlineexam_not_found");
						echo json_encode($retArray);
						exit;
					}
				}
			} else {
				$retArray['message'] = $this->lang->line("onlineexamreport_permissionmethod");
				echo json_encode($retArray);
				exit;
			}
		} else {
			$retArray['message'] = $this->lang->line("onlineexamreport_permission");
			echo json_encode($retArray);
			exit;
		}
	}

	private function getData(&$queryArray)
	{
		$onlineexamID 	= $queryArray['onlineexamID'];
		$classesID 		= $queryArray['classesID'];
		$sectionID 		= $queryArray['sectionID'];
		$studentID 		= $queryArray['studentID'];

		if (isset($queryArray['onlineexamID']) && $queryArray['onlineexamID'] != 0) {
			$onlineexam      					= $this->online_exam_m->get_single_online_exam(['onlineexamID' => $onlineexamID]);
			$this->data['onlineexam']    = $onlineexam->name;
		}

		if (isset($queryArray['classesID']) && $queryArray['classesID'] != 0) {
			$this->data['classesID'] = $classesID;
			$this->data['classes']   = $this->classes_m->get_single_classes(array('classesID' => $classesID));
		} else {
			$this->data['classes'] = array();
		}

		if (isset($queryArray['sectionID']) && ($queryArray['sectionID'] != '' && $queryArray['sectionID'] != 0)) {
			$this->data['sectionID'] = $sectionID;
			$this->data['section']   = $this->section_m->get_single_section(array('sectionID' => $sectionID));
		} else {
			$this->data['section'] = array();
		}

		if (isset($queryArray['studentID']) && $queryArray['studentID'] != 0) {
			$this->data['userID'] = $studentID;
		}

		$this->data['onlineexamID'] = $onlineexamID;
		$this->data['studentID']	= $studentID;
	}
}
