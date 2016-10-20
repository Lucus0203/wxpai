<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session', 'wechat'));
        $this->load->helper(array('form', 'url','captcha'));
        $this->load->model(array('user_model', 'company_model', 'department_model', 'student_model','annualsurvey_model','annualanswer_model'));

    }


    public function index($code)
    {
        $wxinfo = $this->session->userdata('wxinfo');
        if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) && empty($wxinfo)) {
            $this->getwechatcode($code);
            return false;
        } else {
            $userinfo = $this->student_model->get_row("unionid = '" . $wxinfo['unionid'] . "' and unionid<>'' and isdel=2 ");
            if (!empty($userinfo)) {
                $this->session->set_userdata('loginInfo', $userinfo);
                $this->indexRedirect();
                return false;
            }
        }
        $act = $this->input->post('act');
        $error_msg = '';
        if (!empty($act)) {
            $post_company_code = $this->input->post('company_code');
            $mobile = $this->input->post('mobile');
            $pass = $this->input->post('password');
            if(empty($post_company_code)){
                $error_msg = "请输入公司编号";
            }elseif(empty($mobile)){
                $error_msg = "请输入手机号";
            }elseif(empty($pass)){
                $error_msg = "请输入密码";
            }else {
                $userinfo = $this->student_model->get_row(array('mobile' => $mobile, 'company_code' => $post_company_code, 'isdel' => 2));
                $userinfo = empty($userinfo['id']) ? $this->student_model->get_row(array('user_name' => $mobile, 'company_code' => $post_company_code, 'isdel' => 2)) : $userinfo;
                if (!empty($userinfo['id'])) {
                    $pwd = $userinfo ['user_pass'];
                    if ($pwd == md5($pass)) {
                        if (empty($userinfo['unionid'])) {
                            $user['openid'] = $wxinfo['openid'];
                            $user['unionid'] = $wxinfo['unionid'];
                            $user['headimgurl'] = $wxinfo['headimgurl'];
                            $this->student_model->update($user, $userinfo['id']);
                        }
                        if($userinfo['status']==1){//激活
                            $user['register_flag'] = '2';
                            $user['status'] = '2';
                            $this->student_model->update($user, $userinfo['id']);
                        }
                        $this->session->set_userdata('loginInfo', $userinfo);
                        $this->indexRedirect();
                        return false;
                    } else {
                        $error_msg = "密码错误";
                    }
                } else {
                    $error_msg = "账号未注册";
                }
            }
        }
        $company = $this->company_model->get_row(array('code' => $code));
        $this->load->view('login/login', compact('error_msg','company','mobile'));

    }

    private function indexRedirect(){
        $this->initSessionData();
        $action_uri=$this->input->get('action_uri');
        if (!empty($action_uri)) {
            redirect($action_uri);
        } else {
            redirect('course', 'index');
        }
    }

    private function initSessionData(){
        $loginInfo=$this->session->userdata('loginInfo');
        //年度调研
        $survey=$this->annualsurvey_model->get_row("company_code=".$this->db->escape($loginInfo['company_code'])." and unix_timestamp(now()) >= unix_timestamp(time_start) and unix_timestamp(now()) <= unix_timestamp(time_end) and isdel = 2 ");
        $annualSurveyStatus=0;
        if(!empty($survey['id'])){
            $annualSurveyStatus=1;//有问卷
            $answer=$this->annualanswer_model->get_row(array('student_id'=>$loginInfo['id'],'company_code'=>$loginInfo['company_code'],'annual_survey_id'=>$survey['id']));
            if(!empty($answer['id'])){
                $annualSurveyStatus=2;//已回答
            }
        }
        $loginInfo['annualSurveyStatus']=$annualSurveyStatus;
        $this->session->set_userdata('loginInfo',$loginInfo );
    }

    //忘记密码
    public function forgot($company_code){
        $res = array();
        $act = $this->input->post('act');
        $res['company']=$this->company_model->get_row(array('code' => $company_code));
        if (!empty($act)) {
            $user = array('mobile' => $this->input->post('mobile'),
                'user_pass' => $this->input->post('user_pass'),
                'company_code' => $this->input->post('company_code'),
                'mobile_code' => $this->input->post('mobile_code'));
            $res['user']=$user;
            $res['company'] = $company = $this->company_model->get_row(array('code' => $user['company_code']));
            $userinfo = $this->student_model->get_row(array('mobile' => $user['mobile'],'company_code'=>$user['company_code'], 'isdel' => 2));
            if (empty($company)) {
                $msg = '公司编号错误';
            } elseif (!empty($userinfo) && $userinfo['mobile_code'] == $user['mobile_code']) {
                $user['mobile_code'] = rand(1000, 9999);//换个验证码
                $user['user_pass'] = md5($user['user_pass']);
                $this->student_model->update($user, $userinfo['id']);
                $msg = '密码修改成功,请返回登录';
                $res['success']='ok';
                unset($res['user']);
            } else {
                $msg = '短信验证码错误';
            }
            $res['msg']=$msg;
        }
        $res['cap']=$this->getCaptcha();
        $this->load->view('login/forgot', $res);
    }

    //注册1
    public function register1($company_code)
    {
        $res = array();
        $act = $this->input->post('act');
        $res['company']=$this->company_model->get_row(array('code' => $company_code));
        if (!empty($act)) {
            $user = array('mobile' => $this->input->post('mobile'),
                'user_pass' => $this->input->post('user_pass'),
                'company_code' => $this->input->post('company_code'),
                'mobile_code' => $this->input->post('mobile_code'));
            $res['user'] = $user;
            $res['company']=$company = $this->company_model->get_row(array('code' => $user['company_code']));
            $userinfo = $this->student_model->get_row(array('mobile' => $user['mobile'],'company_code'=>$user['company_code'], 'isdel' => 2));
            if (empty($company)) {
                $res['msg'] = '公司编号错误';
            } elseif ($userinfo['register_flag'] == 2) {
                $res['msg'] = '手机号已注册';
            } elseif (!empty($userinfo) && $userinfo['mobile_code'] == $user['mobile_code']) {
                $user['mobile_code'] = rand(1000, 9999);//换个验证码
                $user['user_pass'] = md5($user['user_pass']);
                $user['user_name'] = $user['mobile'];
                $this->student_model->update($user, $userinfo['id']);
                $userinfo = $this->student_model->get_row(array('id' => $userinfo['id']));
                $this->session->set_userdata('loginInfo', $userinfo);
                redirect(site_url('login/register2/' . $userinfo['id']));
            } else {
                $res['msg'] = '短信验证码错误';
            }
        }
        $res['cap']=$this->getCaptcha();

        $this->load->view('login/register_first', $res);
    }

    //完善基本信息
    public function register2()
    {
        $res = array();
        $act = $this->input->post('act');
        $loginInfo = $this->session->userdata('loginInfo');
        if (empty($loginInfo)) {
            redirect(site_url('login/register1'));
        }
        $where = "parent_id is null and company_code = '{$loginInfo['company_code']}' ";
        $deprtments = $this->department_model->get_all($where);
        $second_departments = !empty($studentinfo['department_parent_id'])?$this->department_model->get_all(array('parent_id' => $studentinfo['department_parent_id'],'company_code'=>$loginInfo['company_code'])):array();
        if (!empty($act)) {
            $user = array('name' => $this->input->post('name'),
                'job_code' => $this->input->post('job_code'),
                'job_name' => $this->input->post('job_name'),
                'sex' => $this->input->post('sex'),
                'department_parent_id' => $this->input->post('department_parent_id'),
                'department_id' => $this->input->post('department_id'),
                'email' => $this->input->post('email'),
                'register_flag' => 2,
                'status' => 2);
            //完善微信信息
            $wxinfo = $this->session->userdata('wxinfo');
            $user['openid'] = $wxinfo['openid'];
            $user['unionid'] = $wxinfo['unionid'];
            $user['headimgurl'] = $wxinfo['headimgurl'];
            $this->student_model->update($user, $loginInfo['id']);
            $this->session->set_userdata('loginInfo', $this->student_model->get_row(array('id' => $loginInfo['id'])));
            $this->indexRedirect();
        }

        $this->load->view('login/register_complate', compact('deprtments','second_departments','user'));
    }

    //注册成功
    public function register_success()
    {
        $this->load->view('login/register_success');
    }

    //获取验证码
    public function getcode($forgot='')
    {
        $s=$this->input->server(array('HTTP_REFERER'));
        if(empty($s['HTTP_REFERER'])){
            show_404();
            return false;
        }
        $mobile = $this->input->post('mobile');
        $company_code = $this->input->post('company_code');
        $captcha = $this->input->post('captcha');
        if(empty($mobile)){
            echo '请输入手机号码';
            return false;
        }elseif(empty($company_code)){
            echo '请输入公司编号';
            return false;
        }elseif($this->session->userdata('captcha')=='999999'){
            echo '4位验证码已过期';
            return false;
        }elseif(strtolower($captcha)!=$this->session->userdata('captcha')){
            echo '验证码错误';
            return false;
        }
        if($this->company_model->get_count(array('code'=>$company_code))<=0){
            echo '未找到对应的公司编号';
            return false;
        }
        $userinfo = $this->student_model->get_row(array('mobile' => $mobile,'company_code'=>$company_code, 'isdel' => 2));
        if ($forgot=='forgot') {//忘记密码
            if(empty($userinfo['user_name'])){
                echo '此手机号未注册';
                return false;
            }
        }elseif ($userinfo['register_flag'] == 2) {//注册
            echo '手机号已注册';
            return false;
        }
        $code = rand(1000, 9999);
        if (!empty($userinfo['id'])) {
            $this->student_model->update(array('mobile_code' => $code), $userinfo['id']);
        } else {
            $this->student_model->create(array('mobile' => $mobile, 'mobile_code' => $code, 'company_code'=>$company_code,'created' => date("Y-m-d H:i:s")));
        }
        
        $this->load->library('zhidingsms');
        $this->zhidingsms->sendTPSMS($mobile,'@1@='.$code,'ZD30018-0001');
        $this->session->set_userdata('captcha', '999999');
        echo 1;
    }

    public function setwxinfo($company_code='')
    {
        $orgin_state = $this->session->userdata('wxstate');
        $state = $this->input->get('state');
        $code = $this->input->get('code');
        if ($orgin_state == $state) {
            $tokenData = $this->wechat->getTokenData($code);
            $userData = $this->wechat->getUserInfo($tokenData->access_token, $tokenData->openid);
            $wxinfo = array('access_token' => $tokenData->access_token,
                'refresh_token' => $tokenData->refresh_token,
                'openid' => $tokenData->openid,
                'unionid' => $tokenData->unionid,
                'nickname' => $userData->nickname,
                'sex' => $userData->sex,
                'province' => $userData->province,
                'city' => $userData->city,
                'country' => $userData->country,
                'headimgurl' => $userData->headimgurl);
            $this->session->set_userdata('wxinfo', $wxinfo);
            $weburl=site_url('login/index/'.$company_code);
            redirect($weburl);
        } else {
            $this->getwechatcode($company_code);
        }

    }

    //获取code url
    private function getwechatcode($company_code='')
    {
        $weburl=site_url('login/setwxinfo/'.$company_code);

        $state = rand(10000, 99999);
        $this->session->set_userdata('wxstate', $state);
        $url = $this->wechat->getCodeRedirect($weburl, $state);
        redirect($url);
    }

    public function updateCaptcha(){
        $cap=$this->getCaptcha();
        echo $cap['filename'];
    }

    private function getCaptcha(){
        $vals = array(
            'img_width' => '100',
            'img_height'    => 30,
            'word_length'   => 4,
            'font_size' => 16,
            'img_path'  => './uploads/captcha/',
            'img_url'   => base_url().'uploads/captcha/',
            'pool'      => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'
        );
        $cap = create_captcha($vals);
        $this->session->set_userdata('captcha', strtolower($cap['word']));
        return $cap;
    }

    public function loginout()
    {
        $action_uri=$this->session->userdata('action_uri');
        $logininfo = $this->session->userdata('loginInfo');
        $this->load->database();
        $this->db->query('update ' . $this->db->dbprefix('student') . ' set unionid = NULL where id=' . $logininfo['id']);
        $url=site_url("login/index/".$logininfo['company_code']);
        $url.=!empty($action_uri)?'?action_uri='.$action_uri:'';
        redirect($url);
    }

}
