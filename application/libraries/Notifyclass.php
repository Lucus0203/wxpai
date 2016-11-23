<?php

/**
 * 培训派通知类
 *
 * @version        1.0
 */
class Notifyclass
{
    protected $CI;

    function __construct($config = array())
    {

        $this->CI =& get_instance();
        $this->CI->load->library(array('zhidingsms'));
        $this->CI->load->helper(array('form', 'url'));
        $this->CI->load->model(array('user_model', 'company_model', 'course_model', 'teacher_model', 'homework_model', 'survey_model', 'ratings_model', 'student_model', 'department_model','companytokenwx_model'));

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = '127.0.0.1';
        $config['smtp_user'] = 'mailservice';
        $config['smtp_pass'] = 'service';
        $config['smtp_port'] = '25';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'html';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $this->CI->load->library('email', $config);
    }

    //报名成功 非人工审核
    public function applysuccess($courseid, $studentid)
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        if($course['isnotice_open']!=1){//自动通知关闭
            return false;
        }
        $student = $this->CI->student_model->get_row(array('id' => $studentid));
        $company = $this->CI->company_model->get_row(array('code' => $student['company_code']));
        $t = date('Y年m月d日H时', strtotime($course['time_start']));
        $link = site_url('course/survey/' . $course['id']);
        $link_short='course/survey/' . $course['id'].'.html';
        $sign=$company['name'];
        $sign.=($company['code']=='100276')?' 人力资源部':'';

        //短信通知
        if (!empty($student['mobile'])&&$course['notice_type_msg']==1) {
            /*$msg = "亲爱的{$student['name']}：
你已成功报名参加《{$course['title']}》，该课程将于{$t}在" . $course['address'] . "举行，请提前安排好工作或出差行程，准时参加培训。
上课前请先完成课前调研表（{$link}）和课前作业并提交给我们。
预祝学习愉快，收获满满！

" . $company['name'];
            if($company['code']=='100276'){
                $msg.="
人力资源部";
            }
            $msg.="
". date("Y年m月d日");
            $this->CI->zhidingsms->sendSMS($student['mobile'], $msg);*/
            $content='@1@='.$student['name'].',@2@='.$course['title'].',@3@='.$t.',@4@='.$course['address'].',@5@='.$link_short.',@6@='.$sign.',@7@='.date("Y年m月d日");
            $this->CI->zhidingsms->sendTPSMS($student['mobile'], $content,'ZD30018-0003');
        }

        //mail
        if (!empty($student['email'])&&$course['notice_type_email']==1) {

            $tomail = $student['email'];
            $subject = "《{$course['title']}》报名成功";
            $message = "亲爱的{$student['name']}：
<p style='text-indent:40px'>你已成功报名参加《{$course['title']}》，该课程将于{$t}在{$course['address']}举行，请提前安排好工作或出差行程，准时参加培训。</p>
<p style='text-indent:40px'>上课前请先完成课前调研表（<a href='{$link}' target='_blank'>{$link}</a>）和课前作业并提交给我们。</p>
<p style='text-indent:40px'>预祝学习愉快，收获满满！</p>

<p style=\"text-align: right;margin-right: 40px;\">".$company['name'].'</p>';
            if($company['code']=='100276'){
                $message.='<p style="text-align: right;margin-right: 40px;">人力资源部</p>';
            }
            $message.='<p style="text-align: right;margin-right: 40px;">'. date("Y年m月d日").'</p>';
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();

        }
        //微信通知
        if (!empty($student['openid'])&&$course['notice_type_wx']==1) {
            $wxdata = array(
                'first' => array(
                    'value' => '您好,' . $student['name'] . '
您已成功报名参加' . $course['title'],
                    'color' => "#173177"
                ),
                'class' => array(
                    'value' => $course['title'],
                    'color' => "#173177"
                ),
                'time' => array(
                    'value' => date('m月d日', strtotime($course['time_start'])),
                    'color' => "#173177"
                ),
                'add' => array(
                    'value' => $course['address'],
                    'color' => "#173177"
                ),
                'remark' => array(
                    'value' => "请提前安排好工作或出差行程，准时参加培训。上课前请先完成课前调研表和课前作业并提交给我们。
预祝学习愉快，收获满满！",
                    'color' => "#173177"
                )
            );

            $companyToken=$this->CI->companytokenwx_model->get_row(array('company_code'=>$student['company_code']));
            $this->CI->load->library('wechat', $companyToken);
            //获取templateid
            $objTempid=$this->CI->wechat->getTemplateId('TM00186');
            if($objTempid->errcode=='0') {
                $templateid = $objTempid->template_id;
                $res = $this->CI->wechat->templateSend($student['openid'], $templateid, $link, $wxdata);
            }
        }

    }


}

/* end of file */
