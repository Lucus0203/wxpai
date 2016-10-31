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
            $surveySql="select s.*,aa.step from " . $this->db->dbprefix('annual_answer') . " aa left join " . $this->db->dbprefix('annual_survey') . " s on aa.annual_survey_id=s.id where aa.student_id= ".$this->_logininfo['id']." and s.company_code='".$this->_logininfo['company_code']."' and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 and public=2 ";
            $query=$this->db->query($surveySql);
            $res=$query->row_array();
            $annualSurveyStatus=0;
            if(!empty($res['id'])){
                $annualSurveyStatus=1;//有问卷
                if($res==5){
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
