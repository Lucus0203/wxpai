<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Annual extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','annualsurvey_model','annualquestion_model','annualoption_model','annualanswer_model','annualanswerdetail_model','annualanswercourse_model','annualcourse_model','annualcoursetype_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo['id'])){
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
            $this->load->vars(array('footerNavi'=>'mission'));
        }

    }


    public function answer($surveyid) {
        $survey=$this->annualsurvey_model->get_row(array('id'=>$surveyid));
        if(!empty($surveyid)&&$this->_logininfo['company_code']!=$survey['company_code']){
            $logininfo['company_code']=$survey['company_code'];
            $this->session->set_userdata('loginInfo', $logininfo);
            $this->session->unset_userdata('action_uri');
            redirect(site_url('login/loginout'));return false;
        }
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 and public = 2 ");
        $annualAnswer=$this->annualanswer_model->get_row(array('annual_survey_id'=>$survey['id'],'student_id'=>$this->_logininfo['id']));
        if(empty($survey['id'])){//未发布或已过期
            redirect(site_url('annual/answercomplete').'?err=noexist');return;
        }
        if($annualAnswer['step']==5){
            redirect(site_url('annual/answercomplete'));return;
        }
        if(!$this->isAccessAccount()&&$this->annualanswer_model->get_count(array('company_code'=>$this->_logininfo['company_code'],'annual_survey_id'=>$survey['id'],'step'=>5))>=5){
            redirect(site_url('annual/answercomplete').'?err=above');return;
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
        //判断是否有问题,无则跳过
        $qmodelsql="select module from ".$this->db->dbprefix('annual_question')." q where annual_survey_id=".$survey['id']." group by module ";
        $query=$this->db->query($qmodelsql);
        $modeltyps=$query->result_array();
        if($module!=4){
            $flag=false;
            foreach ($modeltyps as $m){
                if($m['module']==$module){
                    $flag=true;
                }
            }
            if(!$flag){
                //update step
                $this->annualanswer_model->update(array('step'=>$module+1),$annualAnswer['id']);
                redirect(site_url('annual/answer/'.$surveyid));
                return false;
            }
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
        $this->load->view ( 'annual/answer',compact('survey','qatype','modeltyps','questions','courses','msg') );
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
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 and public = 2 ");
        $questions=$this->annualquestion_model->get_all(array('annual_survey_id'=>$survey['id'],'module'=>$module));
        if(empty($survey['id'])){//未发布或已过期
            redirect(site_url('annual/answercomplete').'?err=noexist');return;
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
                $answer_content=$this->input->post('answer_content');
                $this->annualanswer_model->update(array('content'=>$answer_content),$annualAnswerId);
            }
            //update step
            $this->annualanswer_model->update(array('step'=>$module+1),$annualAnswerId);
            if($module==4){
                redirect(site_url('annual/answercomplete'));
            }else{
                redirect(site_url('annual/answer/'.$survey['id']));
            }
        }
    }

    public function answercomplete(){
        $err=$this->input->get('err');

        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($this->_logininfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 and public = 2 ");
        $this->_logininfo['annualSurveyStatus']=2;
        $this->session->set_userdata('loginInfo',$this->_logininfo );
        $this->load->vars(array('loginInfo'=>$this->_logininfo));

        $this->load->view ( 'header' );
        $this->load->view ( 'annual/answercomplete',compact('survey','err'));
        $this->load->view ( 'footer' );
    }


    //是否是正式账号
    private function isAccessAccount(){
        $ordersql="select count(*) as num FROM pai_company_order company_order where company_order.module='annualplan' and company_order.company_code=".$this->_logininfo['company_code']." and company_order.checked=1 and (company_order.use_num=0 or company_order.use_num_remain > 0) and (company_order.years=0 or (date_add(company_order.start_time, interval company_order.years year) > NOW() and company_order.start_time < NOW() ) )";
        $query = $this->db->query($ordersql);
        $res = $query->row_array();
        if($res['num']>0){
            return true;
        }else{
            return false;
        }
    }


}
