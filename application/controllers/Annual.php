<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Annual extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','annualsurvey_model','annualquestion_model','annualoption_model','annualanswer_model','annualanswerdetail_model','annualanswercourse_model','annualcourse_model','annualcoursetype_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo)){
            $objid = $this->uri->segment(3, 0);
            if (!empty($objid)) {
                $survey = $this->annualsurvey_model->get_row(array('id' => $objid));
                $url = site_url('login/index/' . $survey['company_code']).'?action_uri='.current_url();
                redirect($url);
            } else {
                redirect('login', 'index');
            }
        }else{
            $this->load->vars(array('loginInfo'=>$this->_logininfo));
        }

    }


    public function answer($surveyid) {
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 ");
        $annualAnswer=$this->annualanswer_model->get_row(array('annual_survey_id'=>$survey['id'],'student_id'=>$this->_logininfo['id']));
        if(empty($survey['id'])){
            echo '调研问卷不存在或已过期';return;
        }
        if($annualAnswer['step']==5){
            redirect(site_url('annual/answercomplete'));return;
        }
        $step=array('1'=>'acceptance','2'=>'organization','3'=>'requirement','4'=>'coursechosen');
        $qatype=empty($annualAnswer['id'])?$step[1]:$step[$annualAnswer['step']];
        switch ($qatype){
            case 'acceptance':
                $module=1;
                break;
            case 'organization':
                $module=2;
                break;
            case 'requirement':
                $module=3;
                break;
            case 'coursechosen':
                $module=4;
                break;
            default :
                break;
        }
        if($module<4){
            $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$survey['id'],'module'=>$module));
            foreach ($questions as $k=>$q){
                $questions[$k]['options']=$this->annualoption_model->get_all(array('annual_question_id'=>$q['id']));
            }
        }elseif($module==4){
            $courses=$this->annualcoursetype_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$survey['id']));
            foreach ($courses as $kt=>$t){
                $courses[$kt]['courses']=$this->annualcourse_model->get_all(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$survey['id'],'annual_course_type_id'=>$t['id']));
            }
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'annual/answer',compact('survey','qatype','','questions','courses','msg') );
        $this->load->view ( 'footer' );
    }

    public function storeAnswer($qatype='acceptance'){
        $act=$this->input->post('act');
        $module=1;
        switch ($qatype){
            case 'acceptance':
                $module=1;
                break;
            case 'organization':
                $module=2;
                break;
            case 'requirement':
                $module=3;
                break;
            case 'coursechosen':
                $module=4;
                break;
            default :
                break;
        }
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 ");
        $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$survey['id'],'module'=>$module));
        if(empty($survey['id'])){
            echo '调研问卷不存在或已过期';
        }
        if(!empty($survey['id'])&&!empty($act)){
            $annualAnswer=$this->annualanswer_model->get_row(array('annual_survey_id'=>$survey['id'],'student_id'=>$this->_logininfo['id']));
            if(empty($annualAnswer['id'])){
                $annualAnswerId=$this->annualanswer_model->create(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$survey['id'],'student_id'=>$this->_logininfo['id']));
            }else{
                $annualAnswerId=$annualAnswer['id'];
            }
            if($module<4){
                foreach ($questions as $q){
                    $answerDetailOjb=array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$this->_logininfo['id'],'annual_survey_id'=>$survey['id'],'annual_answer_id'=>$annualAnswerId,'annual_question_id'=>$q['id']);
                    $this->annualanswerdetail_model->del($answerDetailOjb);
                    $optionAanswer=$this->input->post('option'.$q['id']);
                    $contentAanswer=$this->input->post('answer_content'.$q['id']);
                    if($q['type']==1){
                        $answerDetailOjb['annual_option_id']=$optionAanswer;
                        $this->annualanswerdetail_model->create($answerDetailOjb);
                    }elseif($q['type']==2){
                        foreach ($optionAanswer as $a){
                            $answerDetailOjb['annual_option_id']=$a;
                            $this->annualanswerdetail_model->create($answerDetailOjb);
                        }
                    }else{
                        $answerDetailOjb['answer_content']=$contentAanswer;
                        $this->annualanswerdetail_model->create($answerDetailOjb);
                    }
                }
            }elseif($module==4){
                $courseids=$this->input->post('course');
                foreach ($courseids as $cid){
                    $this->annualanswercourse_model->create(array('company_code'=>$this->_logininfo['company_code'],'student_id'=>$this->_logininfo['id'],'annual_survey_id'=>$survey['id'],'annual_answer_id'=>$annualAnswerId,'annual_course_id'=>$cid));
                }
            }
            //update step
            $this->annualanswer_model->update(array('step'=>$module+1),$annualAnswerId);

            //跳转到下一页
            switch ($module){
                case '1':
                    $qatype='organization';
                    break;
                case '2':
                    $qatype='requirement';
                    break;
                case '3':
                    $qatype='coursechosen';
                    break;
                case '4':
                    $qatype='completed';
                    break;
                default :
                    break;
            }
            if($module==4){
                redirect(site_url('annual/answercomplete'));
            }else{
                redirect(site_url('annual/answer/'.$qatype));
            }
        }
    }

    public function answercomplete(){
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 ");
        $this->_logininfo['annualSurveyStatus']=2;
        $this->session->set_userdata('loginInfo',$this->_logininfo );
        $this->load->vars(array('loginInfo'=>$this->_logininfo));
        $this->load->view ( 'header' );
        $this->load->view ( 'annual/answercomplete',compact('survey'));
        $this->load->view ( 'footer' );
    }


}
