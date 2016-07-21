<!--head-->
    <header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
            <?php $this->load->view ( 'rightbar' ); ?>
    </header>
    <div class="mConts p0">
            <?php $this->load->view('course/top_navi') ?>
            <div class="proBox">
                    <div class="proInner">
                            <div class="img"><img src="<?php echo empty($course['page_img'])?$this->config->item('pc_url').'images/course_default_img.jpg':$this->config->item('pc_url').'uploads/course_img/'.$course['page_img'] ?>" alt="" width="160"></div>
                            <?php if(!empty($signindata)){?>
                            <a href="javascript:void(0)" class="grayH42 bgGreen fLeft">签到成功</a>
                            <?php }else{ ?>
                                <?php if($course['issignin_open']!=1||$course['signin_start']>date("Y-m-d H:i:s")){ ?>
                                <a href="javascript:void(0)" class="grayH42 fLeft">签到未开始</a>
                                <?php }elseif($course['signin_end']<date("Y-m-d H:i:s")){ ?>
                                <a href="javascript:void(0)" class="grayH42 fLeft">签到已结束</a>
                                <?php }else{ ?>
                                <a href="javascript:void(0)" class="grayH42 bgOrange fLeft">待签到</a>
                            <?php }} ?>
                    </div>
                    <div class="listText">
                        <p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/info/'.$teacher['id']);?>"><?php echo $teacher['name'] ?></a> </p>
                            <p><span class="mr30">开课时间：<?php echo $course['time_start'] ?></span> </p>
                            <p>开课地点：<?php echo $course['address'] ?></p>

                    </div>

            </div>

            <dl class="kecDl">
                <?php if(!empty($course['info'])){ ?>
                <dt>课程介绍</dt>
                <dd>
                        <?php echo nl2br($course['info']) ?>
                </dd>
                <?php } ?>
                <dt>课程收益</dt>
                <dd><?php echo nl2br($course['income']) ?></dd>
                <dt>课程大纲</dt>
                <dd class="noborder">
                        <?php echo nl2br($course['outline']) ?>
                </dd>
            </dl>


    </div>