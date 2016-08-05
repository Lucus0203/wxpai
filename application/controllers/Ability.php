<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Ability extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form','url'));
        $this->load->model(array('student_model','company_model','department_model'));

        $this->_logininfo=$this->session->userdata('loginInfo');
        if(empty($this->_logininfo)){
            redirect('login','index');
        }else{
            $this->load->vars(array('loginInfo'=>$this->_logininfo));
            $this->load->vars(array('homeUrl'=>$this->session->userdata('homeUrl')));
        }

    }


    public function index() {
        $sql = "select job.id,job.name,cpjob.target,cpjob.target_one,cpjob.target_two,cpjob.target_student,cpjob.status from " . $this->db->dbprefix('ability_job') . " job "
            . "left join " .$this->db->dbprefix('company_ability_job')." cpjob on cpjob.ability_job_id = job.id and cpjob.company_code='".$this->_logininfo['company_code']."' "
            . "where job.status = 1 and cpjob.status=1 ";
        $query = $this->db->query($sql);
        $jobs = $query->result_array();

        $this->load->view ( 'header' );
        $this->load->view ( 'center/studentinfo',array('jobs'=>$jobs) );
        $this->load->view ( 'footer' );
    }

}
