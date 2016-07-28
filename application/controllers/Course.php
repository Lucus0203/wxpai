<?php
defined('BASEPATH') or exit ('No direct script access allowed');

class Course extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library(array('session'));
        $this->load->helper(array('form', 'url'));
        $this->load->model(array('user_model', 'course_model', 'teacher_model', 'homework_model', 'homeworklist_model', 'survey_model', 'surveylist_model', 'ratings_model', 'ratingslist_model', 'signinlist_model'));

        $this->load->database();
        $this->_logininfo = $this->session->userdata('loginInfo');
        if (empty($this->_logininfo)) {
            $urictrol = $this->uri->segment(1, 0);
            $uriact = $this->uri->segment(2, 0);//info,signin
            $objid = $this->uri->segment(3, 0);
            if (!empty($uriact) && !empty($objid)) {
                $course = $this->course_model->get_row(array('id' => $objid));
                $url = site_url('login/index/' . $course['company_code']) . '?urictrol=' . $urictrol . '&uriact=' . $uriact . '&uriobjid=' . $objid;
                redirect($url);
            } else {
                redirect('login', 'index');
            }
        } else {
            $this->load->vars(array('loginInfo' => $this->_logininfo));
            $this->load->vars(array('homeUrl' => $this->session->userdata('homeUrl')));
        }

    }


    public function index()
    {
        $this->session->set_userdata('homeUrl', current_url());
        $logininfo = $this->_logininfo;
        //status 1报名中3进行中4结束2待开启报名9其他
        $sql = "select c.*,t.name as teacher,if( unix_timestamp(now()) > unix_timestamp(c.time_end),4,if( unix_timestamp(now()) > unix_timestamp(c.time_start) and unix_timestamp(now()) < unix_timestamp(c.time_end),3,if( isapply_open !=1 ,2,if(unix_timestamp(now()) > unix_timestamp(c.apply_start) and unix_timestamp(now()) < unix_timestamp(c.apply_end),1,9) ) ) ) as status,a.status as apply_status from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "left join " . $this->db->dbprefix('course_apply_list') . " a on a.course_id=c.id and a.student_id={$logininfo['id']} "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 and c.ispublic=1 ";
        $totalres = $this->db->query("select count(*) as num from ($sql) s ")->row_array();
        $total = $totalres['num'];
        $page_num = 6;
        $sql .= " order by status asc,c.id desc limit 0,$page_num ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();
        foreach ($courses as $k => $c) {
            $applycountsql = "select count(*) as num from " . $this->db->dbprefix('course_apply_list') . " a where a.course_id={$c['id']}";
            $query = $this->db->query($applycountsql);
            $applycount = $query->row_array();
            $courses[$k]['apply_count'] = $applycount['num'];
        }

        $this->load->view('header');
        $this->load->view('course/list', array('courses' => $courses, 'total' => $total, 'current_num' => $page_num));
        $this->load->view('footer');

    }

    //加载更多课程
    public function morecourse()
    {
        $logininfo = $this->_logininfo;
        $num = $this->input->post('num');
        //status 1报名中3进行中4结束2待开启报名9其他
        $sql = "select c.*,t.name as teacher,if( unix_timestamp(now()) > unix_timestamp(c.time_end),4,if( unix_timestamp(now()) > unix_timestamp(c.time_start) and unix_timestamp(now()) < unix_timestamp(c.time_end),3,if( isapply_open !=1 ,2,if(unix_timestamp(now()) > unix_timestamp(c.apply_start) and unix_timestamp(now()) < unix_timestamp(c.apply_end),1,9) ) ) ) as status,a.status as apply_status from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "left join " . $this->db->dbprefix('course_apply_list') . " a on a.course_id=c.id and a.student_id={$logininfo['id']} "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 and c.ispublic=1 ";
        $page_num = 6;
        $sql .= " order by status asc,c.id desc limit $num,$page_num ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();
        foreach ($courses as $k => $c) {
            $applycountsql = "select count(*) as num from " . $this->db->dbprefix('course_apply_list') . " a where a.course_id={$c['id']}";
            $query = $this->db->query($applycountsql);
            $applycount = $query->row_array();
            $courses[$k]['apply_count'] = $applycount['num'];
        }
        echo json_encode($courses);

    }

    //我报名的课程
    public function mycourses()
    {
        $this->session->set_userdata('homeUrl', current_url());
        $logininfo = $this->_logininfo;
        //status 1报名中3进行中4结束2待开启报名9其他
        $sql = "select c.*,t.name as teacher,if( unix_timestamp(now()) > unix_timestamp(c.time_end),4,if( unix_timestamp(now()) > unix_timestamp(c.time_start) and unix_timestamp(now()) < unix_timestamp(c.time_end),3,if( isapply_open !=1 ,2,if(unix_timestamp(now()) > unix_timestamp(c.apply_start) and unix_timestamp(now()) < unix_timestamp(c.apply_end),1,9) ) ) ) as status,a.status as apply_status from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "left join " . $this->db->dbprefix('course_apply_list') . " a on a.course_id=c.id "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 and c.ispublic=1 and a.student_id={$logininfo['id']} ";
        $totalres = $this->db->query("select count(*) as num from ($sql) s ")->row_array();
        $total = $totalres['num'];
        $page_num = 6;
        $sql .= " order by status asc,c.id desc limit 0,$page_num ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();
        foreach ($courses as $k => $c) {
            $applycountsql = "select count(*) as num from " . $this->db->dbprefix('course_apply_list') . " a where a.course_id={$c['id']}";
            $query = $this->db->query($applycountsql);
            $applycount = $query->row_array();
            $courses[$k]['apply_count'] = $applycount['num'];
        }

        $this->load->view('header');
        $this->load->view('course/mycourses', array('courses' => $courses, 'total' => $total, 'current_num' => $page_num));
        $this->load->view('footer');

    }

    //加载更多我报名的课程
    public function moremycourse()
    {
        $logininfo = $this->_logininfo;
        $num = $this->input->post('num');
        //status 1报名中3进行中4结束2待开启报名9其他
        $sql = "select c.*,t.name as teacher,if( unix_timestamp(now()) > unix_timestamp(c.time_end),4,if( unix_timestamp(now()) > unix_timestamp(c.time_start) and unix_timestamp(now()) < unix_timestamp(c.time_end),3,if( isapply_open !=1 ,2,if(unix_timestamp(now()) > unix_timestamp(c.apply_start) and unix_timestamp(now()) < unix_timestamp(c.apply_end),1,9) ) ) ) as status,a.status as apply_status from " . $this->db->dbprefix('course') . " c "
            . "left join " . $this->db->dbprefix('teacher') . " t on c.teacher_id=t.id "
            . "left join " . $this->db->dbprefix('course_apply_list') . " a on a.course_id=c.id "
            . "where c.company_code = " . $logininfo['company_code'] . " and c.isdel=2 and c.ispublic=1 and a.student_id={$logininfo['id']} ";
        $page_num = 6;
        $sql .= " order by status asc,c.id desc limit $num,$page_num ";
        $query = $this->db->query($sql);
        $courses = $query->result_array();
        foreach ($courses as $k => $c) {
            $applycountsql = "select count(*) as num from " . $this->db->dbprefix('course_apply_list') . " a where a.course_id={$c['id']}";
            $query = $this->db->query($applycountsql);
            $applycount = $query->row_array();
            $courses[$k]['apply_count'] = $applycount['num'];
        }
        echo json_encode($courses);

    }


    //课程详情
    public function info($id)
    {
        $logininfo = $this->_logininfo;
        $data = array('course_id' => $id, 'student_id' => $logininfo['id']);
        $apply = $this->db->get_where('course_apply_list', $data)->row_array();
        if ($apply['status'] == 1) {
            redirect(site_url('course/applyinfo/' . $id));
            return;
        }

        $course = $this->course_model->get_row(array('id' => $id));
        //获取报名人数
        $applycountsql = "select count(*) as num from " . $this->db->dbprefix('course_apply_list') . " a where a.course_id={$id}";
        $query = $this->db->query($applycountsql);
        $applycount = $query->row_array();
        $course['apply_count'] = $applycount['num'];//报名人数
        $teacher = $this->teacher_model->get_row(array('id' => $course['teacher_id']));
        $this->load->view('header');
        $this->load->view('course/info', array('course' => $course, 'teacher' => $teacher, 'apply' => $apply));
        $this->load->view('footer');
    }


    //课程报名
    public function apply($id)
    {
        $logininfo = $this->_logininfo;
        $act = $this->input->post('act');
        $course = $this->course_model->get_row(array('id' => $id));
        $teacher = $this->teacher_model->get_row(array('id' => $course['teacher_id']));
        $msg = '';
        if (!empty($act)) {
            $data = array('course_id' => $id, 'student_id' => $logininfo['id']);
            $a = $this->db->get_where('course_apply_list', $data)->row_array();
            if (empty($a)) {
                $data['note'] = $this->input->post('note');
                $data['status'] = $course['apply_check'] == 1 ? 3 : 1;
                $this->db->insert('course_apply_list', $data);
            } else {
                $a['note'] = $this->input->post('note');
                $a['status'] = $course['apply_check'] == 1 ? 3 : 1;
                $this->db->where('id', $a['id']);
                $this->db->update('course_apply_list', $a);
            }
            if ($course['apply_check'] != 1) {
                $this->load->library('notifyclass');
                $this->notifyclass->applysuccess($id, $logininfo['id']);
            }
            redirect(site_url('course/applyinfo/' . $id));
        }
        $this->load->view('header');
        $this->load->view('course/apply', array('course' => $course, 'teacher' => $teacher, 'msg' => $msg));
        $this->load->view('footer');
    }

    //报名过课程详情
    public function applyinfo($id)
    {
        $logininfo = $this->_logininfo;
        $data = array('course_id' => $id, 'student_id' => $logininfo['id']);
        $a = $this->db->get_where('course_apply_list', $data)->row_array();
        if (empty($a) || $a['status'] == 2 || $a['status'] == 3) {
            redirect(site_url('course/info/' . $id));
            return;
        }
        $course = $this->course_model->get_row(array('id' => $id));
        $teacher = $this->teacher_model->get_row(array('id' => $course['teacher_id']));
        $signindata = $this->signinlist_model->get_row(array('course_id' => $id, 'student_id' => $logininfo['id']));
        $this->load->view('header');
        $this->load->view('course/applyinfo', array('course' => $course, 'teacher' => $teacher, 'signindata' => $signindata));
        $this->load->view('footer');
    }
    public function applyinfoscan($id)
    {
        $wechat=$this->load->library('wechat');
        $logininfo = $this->_logininfo;
        $data = array('course_id' => $id, 'student_id' => $logininfo['id']);
        $a = $this->db->get_where('course_apply_list', $data)->row_array();
        if (empty($a) || $a['status'] == 2 || $a['status'] == 3) {
            redirect(site_url('course/info/' . $id));
            return;
        }
        $course = $this->course_model->get_row(array('id' => $id));
        $teacher = $this->teacher_model->get_row(array('id' => $course['teacher_id']));
        $signindata = $this->signinlist_model->get_row(array('course_id' => $id, 'student_id' => $logininfo['id']));
        $signPackage=$this->wechat->getSignPackage();
        $this->load->view('header');
        $this->load->view('course/scan_qrcode', array('course' => $course, 'teacher' => $teacher, 'signindata' => $signindata,'signPackage'=>$signPackage));
        $this->load->view('footer');
    }

    //课前调研
    public function survey($courseid)
    {
        $logininfo = $this->_logininfo;
        $course = $this->course_model->get_row(array('id' => $courseid));
        $total = $this->survey_model->count(array('course_id' => $courseid));
        $sql = "select * from " . $this->db->dbprefix('course_survey_list') . " hwlist "
            . " where hwlist.course_id=$courseid and hwlist.student_id=" . $logininfo['id'] . " order by hwlist.id ";
        $query = $this->db->query($sql);
        $survey = $query->result_array();
        if (count($survey) == $total) {//已答过内容展示所有问题与答案
            $this->load->view('header');
            $this->load->view('course/survey_result', array('course' => $course, 'survey' => $survey));
            $this->load->view('footer');
            return;
        }

        //调研分页显示
        $no = $this->input->get('no');
        $no = empty($no) ? 1 : $no;
        $act = $this->input->post('act');
        $content = $this->input->post('content');
        $question = $this->survey_model->get_row(array('course_id' => $courseid, 'num' => $no));
        $answer = $this->surveylist_model->get_row(array('survey_id' => $question['id'], 'student_id' => $logininfo['id']));
        $msg = '';
        if (!empty($act)) {
            if (empty($answer)) {
                $answer = array('course_id' => $courseid, 'survey_id' => $question['id'], 'student_id' => $logininfo['id'], 'title' => $question['title'], 'content' => $content);
                $answerid = $this->surveylist_model->create($answer);
                $answer['id'] = $answerid;
            } else {
                $answer['title'] = $question['title'];
                $answer['content'] = $content;
                $this->surveylist_model->update($answer, $answer['id']);
            }
            if ($no < $total) {
                redirect(site_url('course/survey/' . $courseid) . '?no=' . ($no * 1 + 1));
            } else {
                redirect(site_url('course/survey/' . $courseid));
            }
        }
        $this->load->view('header');
        $this->load->view('course/survey', array('course' => $course, 'question' => $question, 'no' => $no, 'total' => $total, 'answer' => $answer, 'msg' => $msg));
        $this->load->view('footer');
    }

    //课程作业
    public function homework($courseid)
    {
        $logininfo = $this->_logininfo;
        $course = $this->course_model->get_row(array('id' => $courseid));
        $total = $this->homework_model->count(array('course_id' => $courseid));
        $sql = "select * from " . $this->db->dbprefix('course_homework_list') . " hwlist "
            . " where hwlist.course_id=$courseid and hwlist.student_id=" . $logininfo['id'] . " order by hwlist.id ";
        $query = $this->db->query($sql);
        $homework = $query->result_array();
        if (count($homework) == $total) {//已答过内容展示所有问题与答案
            $this->load->view('header');
            $this->load->view('course/homework_result', array('course' => $course, 'homework' => $homework));
            $this->load->view('footer');
            return;
        }
        //问题分页显示
        $no = $this->input->get('no');
        $no = empty($no) ? 1 : $no;
        $act = $this->input->post('act');
        $content = $this->input->post('content');
        $question = $this->homework_model->get_row(array('course_id' => $courseid, 'num' => $no));
        $answer = $this->homeworklist_model->get_row(array('homework_id' => $question['id'], 'student_id' => $logininfo['id']));
        $msg = '';
        if (!empty($act)) {
            if (empty($answer)) {
                $answer = array('course_id' => $courseid, 'homework_id' => $question['id'], 'student_id' => $logininfo['id'], 'title' => $question['title'], 'content' => $content);
                $answerid = $this->homeworklist_model->create($answer);
                $answer['id'] = $answerid;
            } else {
                $answer['title'] = $question['title'];
                $answer['content'] = $content;
                $this->homeworklist_model->update($answer, $answer['id']);
            }
            if ($no < $total) {
                redirect(site_url('course/homework/' . $courseid) . '?no=' . ($no * 1 + 1));
            } else {
                redirect(site_url('course/homework/' . $courseid));
            }
        }
        $this->load->view('header');
        $this->load->view('course/homework', array('course' => $course, 'question' => $question, 'no' => $no, 'total' => $total, 'answer' => $answer, 'msg' => $msg));
        $this->load->view('footer');
    }

    //课程评价
    public function ratings($courseid)
    {
        $logininfo = $this->_logininfo;
        $act = $this->input->post('act');
        $qid = $this->input->post('qid');
        $star = $this->input->post('star');
        $content = $this->input->post('content');
        $course = $this->course_model->get_row(array('id' => $courseid));
        $msg = '';
        if (!empty($act)) {
            if (!empty($qid)) {
                $this->ratingslist_model->del(array('student_id' => $logininfo['id'], 'course_id' => $courseid));
                foreach ($qid as $k => $ratid) {
                    $o = array('course_id' => $courseid, 'ratings_id' => $ratid, 'student_id' => $logininfo['id']);
                    if (!empty($star[$k]))
                        $o['star'] = $star[$k];
                    if (!empty($content[$k]))
                        $o['content'] = $content[$k];
                    $this->ratingslist_model->create($o);
                }
            }
            $msg = '提交成功';
        }
        $question = $this->ratings_model->get_all(array('course_id' => $courseid));
        foreach ($question as $qk => $q) {
            $answer = $this->ratingslist_model->get_row(array('ratings_id' => $q['id'], 'student_id' => $logininfo['id']));
            $question[$qk]['star'] = $answer['star'];
            $question[$qk]['content'] = $answer['content'];
        }
        $this->load->view('header');
        $this->load->view('course/ratings', array('course' => $course, 'question' => $question, 'answer' => $answer, 'msg' => $msg));
        $this->load->view('footer');
    }

    //课程签到
    public function signin($courseid, $qrcode)
    {
        $logininfo = $this->_logininfo;
        $course = $this->course_model->get_row(array('id' => $courseid));
        if ($qrcode == $course['signin_qrcode'] && $course['issignin_open'] == 1 && $course['signin_start'] <= date("Y-m-d H:i:s") && $course['signin_end'] >= date("Y-m-d H:i:s")) {//
            if ($this->signinlist_model->count(array('course_id' => $courseid, 'student_id' => $logininfo['id'])) > 0) {
                $this->signinlist_model->update(array('course_id' => $courseid, 'student_id' => $logininfo['id'], 'signin_time' => date("Y-m-d H:i:s")), array('course_id' => $courseid, 'student_id' => $logininfo['id']));
            } else {
                $this->signinlist_model->create(array('course_id' => $courseid, 'student_id' => $logininfo['id'], 'signin_time' => date("Y-m-d H:i:s")));
            }
            echo '<script type="text/javascript">alert("签到成功");window.location="' . site_url('course/info/' . $courseid) . '";</script>';
            return;
        }
        echo '<script type="text/javascript">alert("签到失败");window.location="' . site_url('course/info/' . $courseid) . '";</script>';

    }

    //课程签退
    public function signout($courseid, $qrcode)
    {
        $logininfo = $this->_logininfo;
        $course = $this->course_model->get_row(array('id' => $courseid));
        if ($qrcode == $course['signout_qrcode'] && $course['issignin_open'] == 1 && $course['signout_start'] <= date("Y-m-d H:i:s") && $course['signout_end'] >= date("Y-m-d H:i:s")) {//
            if ($this->signinlist_model->count(array('course_id' => $courseid, 'student_id' => $logininfo['id'])) > 0) {
                $this->signinlist_model->update(array('course_id' => $courseid, 'student_id' => $logininfo['id'], 'signout_time' => date("Y-m-d H:i:s")), array('course_id' => $courseid, 'student_id' => $logininfo['id']));
            } else {
                $this->signinlist_model->create(array('course_id' => $courseid, 'student_id' => $logininfo['id'], 'signout_time' => date("Y-m-d H:i:s")));
            }
            echo '<script type="text/javascript">alert("签退成功");window.location="' . site_url('course/info/' . $courseid) . '";</script>';
            return;
        }
        echo '<script type="text/javascript">alert("签退失败");window.location="' . site_url('course/info/' . $courseid) . '";</script>';
    }

}
