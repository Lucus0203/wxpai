<!--head-->
<header class="clearfix" id="gHeader">
        <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a>报名申请<a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts">
        <div class="listBox">
                <div class="listCont">

                        <div class="imgBox"><img src="<?php echo $this->config->item('pc_url').'uploads/course_img/'.$course['page_img'] ?>" alt="" width="160"></div>
                        <div class="listText">
                                <p class="titp"><a href="javascript:void(0);"><?php echo $course['title'] ?></a></p>
                                <p>课程讲师：<span class="blue"><?php echo $teacher['name'] ?></span> </p>
                                <p><span class="mr30">开课时间：<?php echo $course['time_start'] ?></span> </p>
                                <p>开课地点：<?php echo $course['address'] ?></p>
                        </div>
                </div>
                <form method="post" action="">
                <div class="iptBox">
                    <input type="hidden" name="act" value="act" />
                    <textarea name="note" class="areaH150" placeholder="请填写申请原因"></textarea>
                        <?php if($course['apply_check']==1){ ?><p class="red">注：该课程报名申请，需经部门经理或管理员审核</p><?php } ?>
                        <?php if(!empty($course['apply_tip'])){ ?><p class="red"><?php echo $course['apply_tip'] ?></p><?php } ?>

                </div>
                <input type="submit" value="提交报名申请" class="blueBtnH40">
                </form>
        </div>

</div>