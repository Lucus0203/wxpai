<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Teacher extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','course_model','teacher_model','annualsurvey_model','annualanswer_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
		if(empty($this->_logininfo['id'])){
            $objid = $this->uri->segment(3, 0);
            if (!empty($objid)) {
                $teacher=$this->teacher_model->get_row(array('id'=>$objid));
                $url = site_url('login/index/' . $teacher['company_code']).'?action_uri='.current_url();
                redirect($url);
            } else {
                redirect('login', 'index');
            }
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
            //年度调研
            $survey=$this->annualsurvey_model->get_row("company_code='".$this->_logininfo['company_code']."' and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 ");
            $annualSurveyStatus=0;
            if(!empty($survey['id'])){
                $annualSurveyStatus=1;//有问卷
                $answer=$this->annualanswer_model->get_row(array('student_id'=>$this->_logininfo['id'],'company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$survey['id']));
                if(!empty($answer['id'])){
                    $annualSurveyStatus=2;//已回答
                }
            }
            $this->load->vars(array('annualSurveyStatus'=>$annualSurveyStatus));
		}
		
	}
        
        //课程详情
        public function info($id){
            $teacher=$this->teacher_model->get_row(array('id'=>$id));
            $this->load->view ( 'header' );
            $this->load->view ( 'teacher/info',array('teacher'=>$teacher) );
            $this->load->view ( 'footer' );
        }
        
	
}
