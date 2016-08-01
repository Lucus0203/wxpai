<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session', 'wechat'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model', 'company_model', 'department_model', 'student_model'));

    }


    public function index($code)
    {
        $wxinfo = array();
//        $wxinfo = $this->session->userdata('wxinfo');
//        if ((strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) && empty($wxinfo)) {
//            redirect('login/getwechatcode');
//        } else {
//            $userinfo = $this->student_model->get_row("unionid = '" . $wxinfo['unionid'] . "' and unionid<>'' and isdel=2 ");
//            if (!empty($userinfo)) {
//                $this->session->set_userdata('loginInfo', $userinfo);
//                redirect('course', 'index');//微信登录
//            }
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
                        $urictrol = $this->input->get('urictrol');
                        $uriact = $this->input->get('uriact');
                        $uriobjid = $this->input->get('uriobjid');
                        if (!empty($uriobjid)) {
                            $url = site_url($urictrol . '/' . $uriact . '/' . $uriobjid);
                            redirect($url);
                        } else {
                            redirect('course', 'index');
                        }
                    } else {
                        $error_msg = "密码错误";
                    }
                } else {
                    $error_msg = "账号未注册";
                }
            }
        }
        $company = $this->company_model->get_row(array('code' => $code));
        $this->load->view('login/login', compact('error_msg','company'));
        //}

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
            $userinfo = $this->student_model->get_row(array('mobile' => $user['mobile'], 'isdel' => 2));
            if (empty($company)) {
                $res['msg'] = '公司编码错误';
            } elseif ($userinfo['register_flag'] == 2) {
                $res['msg'] = '手机号已注册';
            } elseif (!empty($userinfo) && $userinfo['mobile_code'] == $user['mobile_code']) {
                $code = rand(1000, 9999);//换个验证码
                $user['user_pass'] = md5($user['user_pass']);
                $this->student_model->update($user, $userinfo['id']);
                $userinfo = $this->student_model->get_row(array('id' => $userinfo['id']));
                $this->session->set_userdata('loginInfo', $userinfo);
                redirect(site_url('login/register2/' . $userinfo['id']));
            } else {
                $res['msg'] = '验证码错误,请重新获取';
            }
        }

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
        $res['deaprtments'] = $this->department_model->get_all(array('company_code' => $loginInfo['company_code']));
        if (!empty($act)) {
            $user = array('name' => $this->input->post('name'),
                'job_code' => $this->input->post('job_code'),
                'job_name' => $this->input->post('job_name'),
                'department_id' => $this->input->post('department_id'),
                'email' => $this->input->post('email'),
                'register_flag' => 2,
                'status' => 2);
            //完善微信信息
            $wxinfo = $this->session->userdata('wxinfo');
            $user['openid'] = $wxinfo['openid'];
            $user['unionid'] = $wxinfo['unionid'];
            $user['headimgurl'] = $wxinfo['headimgurl'];
            $res['user'] = $user;
            $this->student_model->update($user, $loginInfo['id']);
            $this->session->set_userdata('loginInfo', $this->student_model->get_row(array('id' => $loginInfo['id'])));
            redirect(site_url('course/index'));
        }

        $this->load->view('login/register_complate', $res);
    }

    //注册成功
    public function register_success()
    {
        $this->load->view('login/register_success');
    }

    //获取验证码
    public function getcode()
    {
        $mobile = $this->input->post('mobile');
        $code = 8888;//rand(1000, 9999);
        $userinfo = $this->student_model->get_row(array('mobile' => $mobile, 'isdel' => 2));
        if ($userinfo['register_flag'] == 2) {
            echo '此手机号已注册';
            return false;
        }
        if (!empty($userinfo['id'])) {
            $this->student_model->update(array('mobile_code' => $code), $userinfo['id']);
        } else {
            $this->student_model->create(array('mobile' => $mobile, 'mobile_code' => $code, 'created' => date("Y-m-d H:i:s")));
        }
        echo '1';
        //$this->sms->sendMsg('验证码:'.$code,$mobile);
    }

    public function setwxinfo()
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
            redirect('login/index');
        } else {
            redirect('login/getwechatcode');
        }

    }

    //获取code url
    public function getwechatcode()
    {
        $state = rand(10000, 99999);
        $this->session->set_userdata('wxstate', $state);
        $url = $this->wechat->getCodeRedirect(site_url('login/setwxinfo'), $state);
        redirect($url);
    }

    public function loginout()
    {
        $logininfo = $this->session->userdata('loginInfo');
        $this->load->database();
        $this->db->query('update ' . $this->db->dbprefix('student') . ' set unionid = NULL where id=' . $logininfo['id']);
        $this->session->sess_destroy();
        redirect(site_url("login/index/".$logininfo['company_code']));
    }

}
