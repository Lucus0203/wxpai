<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Annualmanage extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','annualsurvey_model','annualquestion_model','annualoption_model','annualanswer_model','annualanswerdetail_model','annualanswercourse_model','annualcourse_model','annualcoursetype_model','annualplan_model','annualplancourselist_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo['id'])){
            $objid = $this->uri->segment(3, 0);
            if (!empty($objid)) {
                $plan = $this->annualplan_model->get_row(array('id' => $objid));
                $url = site_url('login/index/' . $plan['company_code']).'?action_uri='.current_url();
                redirect($url);
            } else {
                redirect('login', 'index');
            }
        }else{
            $this->load->vars(array('loginInfo'=>$this->_logininfo));
            $this->load->vars(array('footerNavi'=>'mission'));
        }

    }


    public function approved() {
        $plan=$this->annualplan_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'approval_status'=>1));
        if(!empty($plan['id'])){
            $countcourselist="select count(apcl.id) num,apcl.student_id from ".$this->db->dbprefix('annual_plan_course_list')." apcl where annual_plan_id=".$plan['id']." and status=1 group by student_id ";
            $sql="select student.*,countlist.num from ".$this->db->dbprefix('student')." student left join ($countcourselist) countlist on student.id=countlist.student_id ".
                " where student.company_code='".$this->_logininfo['company_code']."' and (student.department_id = ".$this->_logininfo['department_id']." or student.department_parent_id = ".$this->_logininfo['department_id']." ) and student.isdel=2 ";
            $query = $this->db->query($sql);
            $students = $query->result_array();
            $budgetsql=" select round(sum(apc.price/apc.people)) as budget from ".$this->db->dbprefix('annual_plan_course_list')." apcl left join ".$this->db->dbprefix('annual_plan_course')." apc on apc.annual_course_id=apcl.annual_course_id ".
                " left join ".$this->db->dbprefix('student')." student on apcl.student_id = student.id ".
                " where student.company_code='".$this->_logininfo['company_code']."' and (student.department_id = ".$this->_logininfo['department_id']." or student.department_parent_id = ".$this->_logininfo['department_id']." ) and student.isdel=2 and apcl.status=1 and apc.openstatus=1 and apc.people > 0 and apc.annual_plan_id=".$plan['id'];
            $query = $this->db->query($budgetsql);
            $budget = $query->row_array();
            $budget = $budget['budget'];
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'annual/approved',compact('plan','students','budget') );
        $this->load->view ( 'footer' );
    }

    public function approvedlist($planid,$studentid) {
        $this->isAllowPlanid($planid);
        $student=$this->student_model->get_row(array('id'=>$studentid,'company_code'=>$this->_logininfo['company_code']));
        $plan=$this->annualplan_model->get_row(array('id'=>$planid,'company_code'=>$this->_logininfo['company_code']));
        $sql=" select apc.*,apcl.status from ".$this->db->dbprefix('annual_answer_course')." aac left join ".$this->db->dbprefix('annual_plan_course')." apc on apc.annual_course_id=aac.annual_course_id left join ".$this->db->dbprefix('annual_plan_course_list')." apcl on apcl.answer_course_id = aac.id ".
            " where apc.openstatus=1 and apc.annual_plan_id=$planid and aac.student_id = $studentid and aac.annual_survey_id = ".$plan['annual_survey_id'];
        $query = $this->db->query($sql);
        $accourses = $query->result_array();

        $sql=" select apc.*,apcl.status from ".$this->db->dbprefix('annual_plan_course_list')." apcl left join ".$this->db->dbprefix('annual_plan_course')." apc on apc.annual_course_id=apcl.annual_course_id ".
            " where apc.openstatus=1 and apc.annual_plan_id=$planid and apcl.student_id = $studentid and (apcl.answer_course_id = '' or apcl.answer_course_id is null ) ";
        $query = $this->db->query($sql);
        $acplcourses = $query->result_array();
        $courses=array_merge($accourses,$acplcourses);

        $this->load->view ( 'header' );
        $this->load->view ( 'annual/approvedlist',compact('courses','plan','student') );
        $this->load->view ( 'footer' );
    }

    //通过课程审核
    public function approvedcourse($planid,$studentid){
        $this->isAllowPlanid($planid,false);
        $courseid=$this->input->post('course');
        $pc=$this->annualplancourselist_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'student_id'=>$studentid));
        if(!empty($pc['id'])){
            $pc['status']=1;
            $this->annualplancourselist_model->update($pc,array('id'=>$pc['id']));
        }else{
            $plan=$this->annualplan_model->get_row(array('id'=>$planid));
            $asc=$this->annualanswercourse_model->get_row(array('annual_survey_id'=>$plan['annual_survey_id'],'annual_course_id'=>$courseid,'student_id'=>$studentid));
            $this->annualplancourselist_model->create(array('answer_course_id'=>$asc['id'],'company_code'=>$this->_logininfo['company_code'],'student_id'=>$studentid,'annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'status'=>1));
        }
        echo 1;
    }
    //取消审核名单
    public function unapprovedcourse($planid,$studentid){
        $this->isAllowPlanid($planid,false);
        $courseid=$this->input->post('course');
        $pc=$this->annualplancourselist_model->get_row(array('annual_plan_id'=>$planid,'annual_course_id'=>$courseid,'student_id'=>$studentid));
        if(!empty($pc['id'])){
            $pc['status']=2;
            $this->annualplancourselist_model->update($pc,array('id'=>$pc['id']));
        }
        echo 1;
    }

    //是否是自己公司下的计划
    private function isAllowPlanid($planid,$redirect=true){
        if(empty($planid)||$this->annualplan_model->get_count(array('id' => $planid,'company_code'=>$this->_logininfo['company_code']))<=0){
            if($redirect){redirect(site_url('annualmanage/approved'));}
            return false;
        }else{
            return true;
        }
    }


}
