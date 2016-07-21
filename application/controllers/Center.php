<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Center extends CI_Controller {
	
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
	
	
	public function info() {
                $logininfo = $this->_logininfo;
		$act = $this->input->post('act');
		if(!empty($act)){
                    $info=array('name'=>$this->input->post('name'),
                        'job_code'=>$this->input->post('job_code'),
                        'job_name'=>$this->input->post('job_name'),
                        'department_id'=>$this->input->post('department_id'),
                        'mobile'=>$this->input->post('mobile'),
                        'email'=>$this->input->post('email'));
                    $this->student_model->update($info,$logininfo['id']);
                    $msg='资料更新成功';
                }
                $studentinfo=$this->student_model->get_row(array('id'=>$logininfo['id']));
                $departments=$this->department_model->get_all(array('company_code'=>$logininfo['company_code']));
                $this->session->set_userdata('loginInfo',$studentinfo);
		$this->load->vars(array('loginInfo'=>$studentinfo));
		$this->load->view ( 'header' );
		$this->load->view ( 'center/studentinfo',array('departments'=>$departments,'msg'=>$msg) );
		$this->load->view ( 'footer' );
	}
        
        public function changepass(){
                $logininfo = $this->_logininfo;
		$act = $this->input->post('act');
		if(!empty($act)){
                    $cur_pass=$this->input->post('old_pass');
                    $new_pass=$this->input->post('new_pass');
                    if(md5($cur_pass)!=$logininfo['user_pass']){
                        $msg='原密码错误';
                    }else{
                        $this->student_model->update(array('user_pass'=>md5($new_pass)),$logininfo['id']);
                        $msg='密码更新成功';
                    }
                }
                $studentinfo=$this->student_model->get_row(array('id'=>$logininfo['id']));
                $this->session->set_userdata('loginInfo',$studentinfo);
		$this->load->vars(array('loginInfo'=>$studentinfo));
		$this->load->view ( 'header' );
		$this->load->view ( 'center/changepass',array('msg'=>$msg) );
		$this->load->view ( 'footer' );
        }
	
}
