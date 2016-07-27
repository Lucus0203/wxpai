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
        $this->CI->load->library(array('wechat'));
        $this->CI->load->helper(array('form', 'url'));
        $this->CI->load->model(array('user_model', 'company_model', 'course_model', 'teacher_model', 'homework_model', 'survey_model', 'ratings_model', 'student_model', 'department_model'));

        $config['protocol'] = 'smtp';
        $config['smtp_host'] = '127.0.0.1';
        $config['smtp_user'] = 'mailservice';
        $config['smtp_pass'] = 'service';
        $config['smtp_port'] = '25';
        $config['charset'] = 'utf-8';
        $config['mailtype'] = 'text';
        $config['smtp_timeout'] = '5';
        $config['newline'] = "\r\n";
        $this->CI->load->library('email', $config);
    }

    //报名成功 非人工审核
    public function applysuccess($courseid, $studentid)
    {
        $course = $this->CI->course_model->get_row(array('id' => $courseid));
        $student = $this->CI->student_model->get_row(array('id' => $studentid));
        $company = $this->CI->company_model->get_row(array('code' => $student['company_code']));
        //短信通知
        if (!empty($student['mobile'])) {
            $this->CI->load->library('chuanlansms');
            $msg = "
你已成功报名参加《{$course['title']}》课程。该课程将于" . date('m月d日', strtotime($course['time_start'])) . "在" . $course['address'] . "举行，请提前安排好工作或出差行程。
上课前，请先完成课前调研表（" . site_url('course/survey/' . $course['id']) . "）和课前作业，提交给我们。
谢谢你的参与，并祝你学习愉快，取得进步！

" . $company['name'];
            $this->CI->chuanlansms->sendSMS($student['mobile'], $msg);
        }

        //mail
        if (!empty($student['email'])) {

            $tomail = $student['email'];
            $subject = "《{$course['title']}》报名成功";
            $message = "亲爱的{$student['name']}，
你好！
你已成功报名参加《{$course['title']}》课程。该课程将于" . date('m月d日', strtotime($course['time_start'])) . "在" . $course['address'] . "举行，请提前安排好工作或出差行程。
上课前，请先完成课前调研表（" . site_url('course/survey/' . $course['id']) . "）和课前作业，提交给我们。
谢谢你的参与，并祝你学习愉快，取得进步！

" . $company['name'] . "
" . date("m月d日");
            $this->CI->email->from('service@trainingpie.com', '培训派');
            $this->CI->email->to($tomail);//
            $this->CI->email->subject($subject);
            $this->CI->email->message($message);
            $this->CI->email->send();

        }
        //微信通知
        if (!empty($student['openid'])) {
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
                    'value' => "请提前安排好工作或出差行程。
上课前，请先完成课前调研表和课前作业，提交给我们。
谢谢你的参与，并祝你学习愉快，取得进步！",
                    'color' => "#173177"
                )
            );
            $res = $this->CI->wechat->templateSend($student['openid'], 'yFfIfh1EPvvpyeNplv5n6xBEyn5Em4r5ZYAHoLFnM9E', $this->CI->config->item('base_url') . 'course/info/' . $course['id'] . '.html', $wxdata);
        }

    }


}

/* end of file */
