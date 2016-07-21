<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Teacher extends CI_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('session'));
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','course_model','teacher_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
		if(empty($this->_logininfo)){
			redirect('login','index');
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
                        $this->load->vars(array('homeUrl'=>$this->session->userdata('homeUrl')));
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
