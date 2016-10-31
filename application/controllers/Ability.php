<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','department_model','ability_model','abilityjob_model','companyabilityjob_model','annualsurvey_model','annualanswer_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo['id'])){
            $company_code = $this->uri->segment(4, 0);
            if (!empty($company_code)) {
                $url = site_url('login/index/' . $company_code).'?action_uri='.current_url();
                redirect($url);
            } else {
                redirect('login', 'index');
            }
        }else{
            $this->session->unset_userdata('action_uri');
            $this->load->vars(array('loginInfo'=>$this->_logininfo));
            $this->load->vars(array('homeUrl' => site_url('ability/index')));
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
        $sql = "select ability.*,if(cajm.model_name!='',cajm.model_name,ability.name) as model_name from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = job_model.model_id and cajm.job_id=$abilityjob_id and cajm.company_code='".$this->_logininfo['company_code']."' "
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
        $modnames = $this->input->post('modname');
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
            $abilityObj=array();
            $m=$this->ability_model->get_row(array('id'=>$mid));
            $abilityObj['company_code']=$company_code;
            $abilityObj['ability_job_id']=$abilityjob_id;
            $abilityObj['student_id']=$this->_logininfo['id'];
            $abilityObj['point']=$v;
            $abilityObj['type']=$m['type'];
            $abilityObj['model_id']=$mid;
            $abilityObj['name']=$modnames[$mid];
            $abilityObj['info']=$m['info'];
            $abilityObj['level']=$m['level'];
            $abilityObj['level_info1']=$m['level_info1'];
            $abilityObj['level_info2']=$m['level_info2'];
            $abilityObj['level_info3']=$m['level_info3'];
            $abilityObj['level_info4']=$m['level_info4'];
            $abilityObj['level_info5']=$m['level_info5'];
            $abilityObj['level_info6']=$m['level_info6'];
            $abilityObj['level_info7']=$m['level_info7'];
            $abilityObj['level_info8']=$m['level_info8'];
            $abilityObj['level_info9']=$m['level_info9'];
            $abilityObj['level_info10']=$m['level_info10'];
            $this->db->insert ( 'company_ability_job_student_assess', $abilityObj );
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
        //获取岗位评分标准
        $sql = "select ability.type,if(cajm.level_standard!='',cajm.level_standard,job_model.level_standard) as level_standard from " . $this->db->dbprefix('ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('ability_model') . " ability on ability.id = job_model.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = job_model.model_id and cajm.job_id=$abilityjob_id and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where job_model.job_id = $abilityjob_id ";
        $sql.=" order by ability.type asc,job_model.id asc";
        $query = $this->db->query("select s.type,sum(level_standard) as point_standard from ($sql) s group by s.type ");
        $res = $query->result_array();
        $standard=array();
        foreach ($res as $s){
            $standard[$s['type']]=$s['point_standard'];
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/result',compact('abilities','abilityjob','standard'));
        $this->load->view ( 'footer' );
    }

    public function resultdetail($abilityjob_id){
        $sql = "select assess.*,if(cajm.level_standard!='',cajm.level_standard,job_model.level_standard) as level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "left join " . $this->db->dbprefix('ability_job_model') . " job_model on job_model.id = assess.model_id "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.model_id = assess.model_id and cajm.job_id=$abilityjob_id and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and student_id=".$this->_logininfo['id'];
        $query = $this->db->query($sql . " order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $abilityjob=$this->abilityjob_model->get_row(array('id'=>$abilityjob_id));
        $this->load->view ( 'header' );
        $this->load->view ( 'ability/resultdetail',compact('abilities','abilityjob'));
        $this->load->view ( 'footer' );
    }

    public function resultrecommend($abilityjob_id){
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
        $this->load->view ( 'ability/resultrecommend',compact('abilities','abilityjob'));
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
