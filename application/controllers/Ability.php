<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','department_model','ability_model','abilityjob_model','companyabilityjob_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo)){
            redirect('login','index');
        }else{
            $this->load->vars(array('loginInfo'=>$this->_logininfo));
            $this->load->vars(array('homeUrl'=>$this->session->userdata('homeUrl')));
        }

    }


    public function index() {
        //complate_status 1待评估2评估完成
        $sql = "select abilityjob.*,cajs.status as complate_status,cajs.point,cajs.updated as complate_time from " . $this->db->dbprefix('company_ability_job_student') . " cajs "
            . "left join " .$this->db->dbprefix('ability_job')." abilityjob on cajs.ability_job_id = abilityjob.id "
            . "left join " .$this->db->dbprefix('company_ability_job')." caj on caj.ability_job_id = abilityjob.id and caj.company_code='".$this->_logininfo['company_code']."' "
            . "where cajs.student_id = ".$this->_logininfo['id']." and cajs.isdel=2 and caj.status=1 and cajs.company_code = '".$this->_logininfo['company_code']."'";
        $query = $this->db->query($sql);
        $jobs = $query->result_array();

        $this->load->view ( 'header' );
        $this->load->view ( 'ability/index',array('jobs'=>$jobs) );
        $this->load->view ( 'footer' );
    }

    public function assess($abilityjob_id){
        //如果未发布则跳转
        $this->ispublishJob($abilityjob_id);
        //type1专业能力2通用能力3领导力4个性5经验
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjob_id));
        $sql="select count(*) as num,type from ".$this->db->dbprefix('ability_job_model')." jobmodel 
        left join ".$this->db->dbprefix('ability_model')." model on jobmodel.model_id=model.id 
        where jobmodel.job_id = $abilityjob_id group by model.type order by model.type ";
        $query=$this->db->query($sql);
        $countarry=$query->result_array();
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/assess',compact('abilityjob','countarry') );
        $this->load->view ( 'footer' );
    }

    public function evaluate($abilityjob_id){
        //如果未发布则跳转
        $this->ispublishJob($abilityjob_id);
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjob_id));
        $sql = "select ability.* from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "where job_model.job_id = $abilityjob_id ";
        $query = $this->db->query($sql . " order by ability.type asc,job_model.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/evaluate',compact('abilities','abilityjob') );
        $this->load->view ( 'footer' );
    }

    public function evaluatestore(){
        $company_code=$this->_logininfo['company_code'];
        $abilityjob_id = $this->input->post('abilityjob_id');
        $modids = $this->input->post('modid');
        //如果未发布则跳转
        $this->ispublishJob($abilityjob_id);
        $query=$this->db->get_where('company_ability_job_student',array('company_code'=>$company_code,'ability_job_id'=>$abilityjob_id,'student_id'=>$this->_logininfo['id']));
        $cajs=$query->row_array();
        if(count($modids)<=0||$cajs['status']!=1||$cajs['isdel']==1){//非待评估状态则跳转
            redirect('ability','index');
            return false;
        }
        $totalPoint=0;
        foreach ($modids as $mid => $v){
            $abilityObj=$this->ability_model->get_row(array('id'=>$mid));
            unset($abilityObj['id']);
            unset($abilityObj['code']);
            unset($abilityObj['created']);
            unset($abilityObj['updated']);
            $abilityObj['company_code']=$company_code;
            $abilityObj['ability_job_id']=$abilityjob_id;
            $abilityObj['student_id']=$this->_logininfo['id'];
            $abilityObj['point']=$v;
            $this->db->insert ( 'company_ability_job_student_assess', $abilityObj );
            echo $this->db->last_query();
            print_r($modids);exit();
            $totalPoint+=$v/$abilityObj['level'];
        }
        $totalPoint=count($modids)>0?$totalPoint/count($modids)*5:0;//总分5
        $this->db->where ( array('company_code'=>$company_code,'ability_job_id'=>$abilityjob_id,'student_id'=>$this->_logininfo['id']) );
        $this->db->update ( 'company_ability_job_student', array('status'=>2,'point'=>$totalPoint) );
        redirect(site_url('ability/result/'.$abilityjob_id));
    }

    public function result($abilityjob_id){
        $sql = "select sum(point) as point,sum(level) as level,type from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and student_id=".$this->_logininfo['id'];
        $query = $this->db->query($sql . " group by type order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']]=$a;
        }
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjob_id));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/result',compact('abilities','abilityjob'));
        $this->load->view ( 'footer' );
    }

    public function resultdetail($abilityjob_id){
        $sql = "select count(*) as num,sum(point) as point,sum(level) as level,type from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and student_id=".$this->_logininfo['id'];
        $query = $this->db->query($sql . " group by type order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']]=$a;
        }
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjob_id));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/resultdetail',compact('abilities','abilityjob'));
        $this->load->view ( 'footer' );
    }

    private function ispublishJob($abilityjob_id){
        //如果未发布则跳转
        $companyjob=$this->companyabilityjob_model->get_row(array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$abilityjob_id));
        $query=$this->db->get_where('company_ability_job_student',array('company_code'=>$this->_logininfo['company_code'],'ability_job_id'=>$abilityjob_id,'student_id'=>$this->_logininfo['id']));
        $cajs=$query->row_array();
        if(empty($companyjob)||$companyjob['status']!=1||empty($cajs)||$cajs['isdel']==1){
            redirect('ability','index');
            return false;
        }
        if($cajs['status']==2){
            redirect(site_url('ability/result/'.$abilityjob_id));
            return false;
        }
    }

}
