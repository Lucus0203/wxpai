<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Index extends CI_Controller {
	var $_logininfo;
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form','url'));
		$this->load->model(array('user_model','course_model','teacher_model','homework_model'));
		
		$this->_logininfo=$this->session->userdata('loginInfo');
		if(empty($this->_logininfo)){
			redirect('login','index');
		}else{
			$this->load->vars(array('loginInfo'=>$this->_logininfo));
		}
	}
	
	
	public function index() {
            redirect(site_url('course/index'));
	}
}
