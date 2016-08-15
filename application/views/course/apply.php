<!--head-->
<script>
    function checkApplyForm(){
        if($.trim($('textarea[name=note]').val())==''){
            $('.error').text('请填写申请原因');
            return false;
        }else{
            return true;
        }
    }
</script>
<header class="clearfix" id="gHeader">
    <div class="header"><a href="<?php echo site_url('course/info/' . $course['id']); ?>"><i class="iright">◇</i></a>报名申请<a
            href="javascript:void(0);"><i class="ilevel">=</i></a></div>
    <?php $this->load->view('rightbar'); ?>
</header>
<div class="mConts">
    <div class="listBox">
        <div class="listCont">

            <div class="imgBox"><img
                    src="<?php echo $this->config->item('pc_url') . 'uploads/course_img/' . $course['page_img'] ?>"
                    alt="" width="160"></div>
            <div class="listText">
                <p class="titp"><a class="blue"
                                   href="<?php echo site_url('course/info/' . $course['id']); ?>"><?php echo $course['title'] ?></a>
                </p>
                <?php if (!empty($teacher['name'])) { ?><p>课程讲师：<a class="blue"
                                                                   href="<?php echo site_url('teacher/info/' . $teacher['id']); ?>"><?php echo $teacher['name'] ?></a>
                </p><?php } ?>
                <p>开始时间：<?php echo date("m-d H:i", strtotime($course['time_start'])) ?></p>
                <p>结束时间：<?php echo date("m-d H:i", strtotime($course['time_end'])) ?></p>
                <p>开课地点：<?php echo $course['address'] ?></p>
            </div>
        </div>
        <form id="applyform" method="post" action="" onsubmit="return checkApplyForm()">
            <div class="iptBox">
                <input type="hidden" name="act" value="act"/>
                <textarea name="note" class="areaH150" placeholder="请填写申请原因"></textarea>
                <p class="red error" style="display: none;"></p>
                <?php if ($course['apply_check'] == 1) { ?><p class="red">注：该课程报名申请，需经部门经理或管理员审核</p><?php } ?>
                <?php if (!empty($course['apply_tip'])) { ?><p class="red"><?php echo $course['apply_tip'] ?></p><?php } ?>

            </div>
            <input type="submit" value="提交报名申请" class="blueBtnH40">
        </form>
    </div>

</div>