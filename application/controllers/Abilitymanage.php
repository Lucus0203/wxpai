<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Abilitymanage extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','department_model','companyabilityjob_model','companyabilityjoblevel_model','companyabilitymodel_model','companyabilityjobevaluation_model','annualsurvey_model','annualanswer_model'));

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
            $this->load->vars(array('footerNavi'=>'mission'));
        }

    }


    public function index() {
        $sql = " select cajs.*,group_concat(student.name) as students from " . $this->db->dbprefix('company_ability_job_evaluation_student') . " cajs "
            . " left join " .$this->db->dbprefix('student')." student on cajs.student_id = student.id "
            . " where student.company_code='".$this->_logininfo['company_code']."' and (student.department_id = ".$this->_logininfo['department_id']." or student.department_parent_id = ".$this->_logininfo['department_id']." ) and student.isdel=2 and student.id !=".$this->_logininfo['id']." and cajs.isdel=2 "
            . " group by ability_job_evaluation_id ";

        $totalres = $this->db->query("select count(*) as num from ($sql) s ")->row_array();
        $total = $totalres['num'];
        $page_num = 6;
        $sql .= " order by id desc  limit 0,$page_num ";
        $query = $this->db->query($sql);
        $jobs = $query->result_array();
        foreach ($jobs as $k=>$j){
            $sql="select evaluation.name,abilityjob.name as ability_name,abilityjoblevel.name as level,evaluation.time_end from " .$this->db->dbprefix('company_ability_job_evaluation')." evaluation "
                ." left join ".$this->db->dbprefix('company_ability_job')." abilityjob on abilityjob.id = evaluation.ability_job_id "
                ." left join ".$this->db->dbprefix('company_ability_job_level ')." abilityjoblevel on abilityjoblevel.id = abilityjob.ability_job_level_id "
                ." where evaluation.id = ".$j['ability_job_evaluation_id'];
            $query = $this->db->query($sql);
            $obj = $query->row_array();
            $jobs[$k]['name']=$obj['name'];
            $jobs[$k]['ability_name']=$obj['ability_name'];
            $jobs[$k]['level']=$obj['level'];
            $jobs[$k]['time_end']=$obj['time_end'];
        }

        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/index',array('jobs'=>$jobs,'total' => $total, 'current_num' => $page_num) );
        $this->load->view ( 'footer' );
    }

    public function more(){
        $num = $this->input->post('num');
        $sql = " select cajs.*,group_concat(student.name) as students from " . $this->db->dbprefix('company_ability_job_evaluation_student') . " cajs "
            . " left join " .$this->db->dbprefix('student')." student on cajs.student_id = student.id "
            . " where student.company_code='".$this->_logininfo['company_code']."' and (student.department_id = ".$this->_logininfo['department_id']." or student.department_parent_id = ".$this->_logininfo['department_id']." ) and student.isdel=2 and student.id !=".$this->_logininfo['id']." and cajs.isdel=2 "
            . " group by ability_job_evaluation_id ";
        $page_num = 6;
        $sql .= " order by id desc  limit $num,$page_num ";
        $query = $this->db->query($sql);
        $jobs = $query->result_array();
        foreach ($jobs as $k=>$j){
            $sql="select evaluation.name,abilityjob.name as ability_name,abilityjoblevel.name as level,evaluation.time_end from " .$this->db->dbprefix('company_ability_job_evaluation')." evaluation "
                ." left join ".$this->db->dbprefix('company_ability_job')." abilityjob on abilityjob.id = evaluation.ability_job_id "
                ." left join ".$this->db->dbprefix('company_ability_job_level ')." abilityjoblevel on abilityjoblevel.id = abilityjob.ability_job_level_id "
                ." where evaluation.id = ".$j['ability_job_evaluation_id'];
            $query = $this->db->query($sql);
            $obj = $query->row_array();
            $jobs[$k]['name']=$obj['name'];
            $jobs[$k]['ability_name']=$obj['ability_name'];
            $jobs[$k]['level']=$obj['level'];
            $jobs[$k]['time_end']=date('m-d H:i',strtotime($obj['time_end']));
        }
        echo json_encode($jobs);
    }

    //给同部门的员工评估
    public function staffevaluation($evaluationid){
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $level=$this->companyabilityjoblevel_model->get_row(array('id'=>$abilityjob['ability_job_level_id']));
        $sql=" select student.id as student_id,student.name,others.name as others_name,cajs.ability_job_evaluation_id,cajs.status,cajs.others_status from " . $this->db->dbprefix('company_ability_job_evaluation_student') . " cajs "
            . " left join " .$this->db->dbprefix('student')." student on cajs.student_id = student.id "
            . " left join " .$this->db->dbprefix('student')." others on cajs.others_id = others.id "
            . " where cajs.company_code='".$this->_logininfo['company_code']."' and cajs.ability_job_evaluation_id = $evaluationid and student.company_code='".$this->_logininfo['company_code']."' and (student.department_id = ".$this->_logininfo['department_id']." or student.department_parent_id = ".$this->_logininfo['department_id']." ) and student.isdel=2 and student.id != ".$this->_logininfo['id'];
        $query = $this->db->query($sql);
        $students = $query->result_array();

        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/staffevaluation',array('evaluation' => $evaluation,'abilityjob'=>$abilityjob,'level'=>$level,'students' => $students) );
        $this->load->view ( 'footer' );

    }

    public function assess($evaluationid,$studentid){
        //如果未发布则跳转
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        //type1专业能力2通用能力3领导力4个性5经验
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $sql="select count(*) as num,type from ".$this->db->dbprefix('company_ability_job_model')." jobmodel 
        where jobmodel.ability_job_id = ".$evaluation['ability_job_id']." group by jobmodel.type order by jobmodel.type ";
        $query=$this->db->query($sql);
        $countarry=$query->result_array();
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/assess',compact('evaluation','abilityjob','countarry','student') );
        $this->load->view ( 'footer' );
    }

    public function evaluate($evaluationid,$studentid){
        //如果未发布则跳转
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $sql = "select ability.* from " . $this->db->dbprefix('company_ability_job_model') . " job_model "
            . "left join " . $this->db->dbprefix('company_ability_model') . " ability on ability.id = job_model.ability_model_id "
            . "where job_model.ability_job_id = ".$abilityjob['id']." and ability.company_code='".$this->_logininfo['company_code']."' ";
        $query = $this->db->query($sql . " order by ability.type asc,job_model.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/evaluate',compact('evaluation','abilities','abilityjob','student') );
        $this->load->view ( 'footer' );
    }

    public function evaluatestore(){
        $company_code=$this->_logininfo['company_code'];
        $evaluationid = $this->input->post('evaluation_id');
        $studentid = $this->input->post('student_id');
        $modids = $this->input->post('modid');
        $modnames = $this->input->post('modname');
        //如果未发布则跳转
        $this->isAlowEvaluationid($evaluationid,$studentid);
        if(count($modids)<=0){//未填写数据则跳转
            redirect('ability','index');
            return false;
        }
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $totalPoint=$totalLevel=0;
        $this->db->where (array('isothersevaluation'=>1,'ability_job_evaluation_id'=>$evaluationid,'student_id'=>$studentid));
        $this->db->delete ( 'company_ability_job_student_assess' );
        foreach ($modids as $mid => $v){
            $abilityObj=array();
            $m=$this->companyabilitymodel_model->get_row(array('id'=>$mid));
            $abilityObj['company_code']=$company_code;
            $abilityObj['ability_job_id']=$evaluation['ability_job_id'];
            $abilityObj['ability_job_evaluation_id']=$evaluationid;
            $abilityObj['student_id']=$studentid;
            $abilityObj['isothersevaluation']=1;
            $abilityObj['others_id']=$this->_logininfo['id'];
            $abilityObj['point']=$v;
            $abilityObj['type']=$m['type'];
            $abilityObj['ability_model_id']=$mid;
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
            $totalPoint+=$v;
            $totalLevel+=$m['level']*1;
        }
        $point=count($totalLevel)>0?$totalPoint/$totalLevel*5:0;//总分5
        $this->db->where ( array('company_code'=>$company_code,'ability_job_evaluation_id'=>$evaluationid,'student_id'=>$studentid) );
        $this->db->update ( 'company_ability_job_evaluation_student', array('others_id'=>$this->_logininfo['id'],'others_status'=>2,'others_point'=>$point) );
        redirect(site_url('abilitymanage/resultother/'.$evaluationid.'/'.$studentid));
    }

    public function result($evaluationid,$studentid){
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $abilityjob_id=$abilityjob['id'];
        $sql = "select sum(point) as point,sum(level) as level,type from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and assess.isothersevaluation=2 and student_id=".$studentid;
        $query = $this->db->query($sql . " group by type order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']]=$a;
        }
        //获取岗位评分标准
        $sql = "select type,level_standard from " . $this->db->dbprefix('company_ability_job_model') . " job_model "
            . "where job_model.company_code='".$this->_logininfo['company_code']."' and job_model.ability_job_id = $abilityjob_id ";
        $sql.=" order by job_model.type asc,job_model.id asc";
        $query = $this->db->query("select s.type,sum(level_standard) as point_standard from ($sql) s group by s.type ");
        $res = $query->result_array();
        $standard=array();
        foreach ($res as $s){
            $standard[$s['type']]=$s['point_standard'];
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/result',compact('evaluation','abilities','abilityjob','standard','student'));
        $this->load->view ( 'footer' );
    }

    public function resultdetail($evaluationid,$studentid){
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $abilityjob_id=$abilityjob['id'];
        $sql = "select assess.*,cajm.level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.ability_model_id = assess.ability_model_id and cajm.ability_job_id=$abilityjob_id and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and assess.isothersevaluation=2 and student_id=".$studentid;
        $query = $this->db->query($sql . " order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/resultdetail',compact('evaluation','abilities','abilityjob','student'));
        $this->load->view ( 'footer' );
    }

    public function resultother($evaluationid,$studentid){
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $abilityjob_id=$abilityjob['id'];
        $sql = "select sum(point) as point,sum(level) as level,type from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and assess.isothersevaluation=1 and student_id=".$studentid;
        $query = $this->db->query($sql . " group by type order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']]=$a;
        }
        //获取岗位评分标准
        $sql = "select type,level_standard from " . $this->db->dbprefix('company_ability_job_model') . " job_model "
            . "where job_model.company_code='".$this->_logininfo['company_code']."' and job_model.ability_job_id = $abilityjob_id ";
        $sql.=" order by job_model.type asc,job_model.id asc";
        $query = $this->db->query("select s.type,sum(level_standard) as point_standard from ($sql) s group by s.type ");
        $res = $query->result_array();
        $standard=array();
        foreach ($res as $s){
            $standard[$s['type']]=$s['point_standard'];
        }
        $isother=true;
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/result',compact('evaluation','abilities','abilityjob','standard','student','isother'));
        $this->load->view ( 'footer' );
    }

    public function resultdetailother($evaluationid,$studentid){
        $this->isAlowEvaluationid($evaluationid,$studentid);
        $student=$this->student_model->get_row(array('id'=>$studentid));
        $evaluation=$this->companyabilityjobevaluation_model->get_row(array('id'=>$evaluationid));
        $abilityjob=$this->companyabilityjob_model->get_row(array('id'=>$evaluation['ability_job_id']));
        $abilityjob_id=$abilityjob['id'];
        $sql = "select assess.*,cajm.level_standard from " . $this->db->dbprefix('company_ability_job_student_assess') . " assess "
            . "left join " . $this->db->dbprefix('company_ability_job_model') . " cajm on cajm.ability_model_id = assess.ability_model_id and cajm.ability_job_id=$abilityjob_id and cajm.company_code='".$this->_logininfo['company_code']."' "
            . "where assess.company_code = '".$this->_logininfo['company_code']."' and assess.ability_job_id=$abilityjob_id and assess.isothersevaluation=1 and student_id=".$studentid;
        $query = $this->db->query($sql . " order by assess.type asc,assess.id asc ");
        $res = $query->result_array();
        $abilities=array();
        foreach ($res as $a){
            $abilities[$a['type']][]=$a;
        }
        $isother=true;
        $this->load->view ( 'header' );
        $this->load->view ( 'abilitymanage/resultdetail',compact('evaluation','abilities','abilityjob','student','isother'));
        $this->load->view ( 'footer' );
    }

    private function isAlowEvaluationid($evaluationid,$studentid){
        //如果未发布则跳转
        $where="company_code='".$this->_logininfo['company_code']."' and ability_job_evaluation_id=$evaluationid and student_id=$studentid and isdel=2 ";
        $query = $this->db->get_where ( 'company_ability_job_evaluation_student', $where );
        $cajes=$query->row_array();
        if(empty($cajes['id'])){
            redirect('abilitymanage','index');
            return false;
        }

    }

}
