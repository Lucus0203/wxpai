<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Mission extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url','download'));
        $this->load->model(array('user_model','student_model', 'course_model', 'teacher_model', 'homework_model', 'homeworklist_model', 'survey_model', 'surveylist_model', 'ratings_model', 'ratingslist_model', 'signinlist_model','prepare_model','annualsurvey_model','annualanswer_model','annualplan_model'));

        $this->load->database();
        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo['id'])) {
                redirect('login', 'index');
        } else {
            $this->session->unset_userdata('action_uri');
            $this->load->vars(array('loginInfo' => $this->_logininfo));
            $this->load->vars(array('footerNavi'=>'mission'));
        }

    }


    public function index(){
        $loginInfo=$this->student_model->get_row(array('id'=>$this->_logininfo['id']));
        $this->load->vars(array('loginInfo' => $loginInfo));
        //年度调研
        $surveySql="select s.*,aa.step from " . $this->db->dbprefix('annual_answer') . " aa left join " . $this->db->dbprefix('annual_survey') . " s on aa.annual_survey_id=s.id where aa.student_id= ".$this->_logininfo['id']." and s.company_code='".$this->_logininfo['company_code']."' and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 and public=2 ";
        $query=$this->db->query($surveySql);
        $res=$query->row_array();
        $annualSurveyStatus=0;
        if(!empty($res['id'])){
            $annualSurveyStatus=1;//有问卷
            if($res['step']==5){
                $annualSurveyStatus=2;//已回答
            }
        }
        //年度计划审核
        $plancount=$this->annualplan_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'approval_status'=>1));

        $this->load->view('header');
        $this->load->view('mission/index',compact('annualSurveyStatus','plancount'));
        $this->load->view('footer');

    }

}
