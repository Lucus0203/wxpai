<!--head-->
<header class="clearfix mb0" id="gHeader">
        <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="proCont">
                <div class="imgBox">
                        <img src="<?php echo empty($course['page_img'])?$this->config->item('pc_url').'images/course_default_img.jpg':$this->config->item('pc_url').'uploads/course_img/'.$course['page_img'] ?>" alt="" width="160">
                </div>

                <div class="listText">
                    <p>
                        <?php  if(strtotime($course['apply_end']) < time()){ ?>
                            <span class="grayH25">已结束</span>
                        <?php }else{ ?>
                            <?php if($apply['status']==3){ ?><span class="orangeH25">待审核</span>
                            <?php }elseif($apply['status']==2){ ?><span class="redH25">报名被拒</span>
                            <?php }elseif($apply['status']==1){ ?><span class="greenH25">报名成功</span>
                            <?php }else{ ?><span class="orangeH25">未报名</span><?php } ?>
                        <?php } ?>
                    </p>
                        <?php if(!empty($teacher['name'])){?>
                        <p class="mt10"><span class="mr30">课程讲师：<a class="white" href="<?php echo site_url('teacher/info/'.$teacher['id']);?>"><?php echo $teacher['name'] ?></a></span> </p>
                        <?php } ?>
                        <p <?php if(empty($teacher['name'])){ echo 'class="mt10"';} ?> >开始时间：<?php echo date("m-d H:i",  strtotime($course['time_start'])) ?></p>
                        <p>结束时间：<?php echo date("m-d H:i",  strtotime($course['time_end'])) ?></p>
                        <p>开课地点：<?php echo $course['address'] ?></p>
                </div>
        </div>
<div class="mConts">

        <dl class="kecDl">
            <?php if(!empty($course['info'])){ ?>
                <dt>课程介绍</dt>
                <dd><?php echo nl2br($course['info']) ?></dd>
            <?php } ?>
            <?php if(!empty($course['income'])){ ?>
                <dt>课程收益</dt>
                <dd><?php echo nl2br($course['income']) ?></dd>
            <?php } ?>
                <dt>课程大纲</dt>
                <dd class="noborder">
                        <?php echo nl2br($course['outline']) ?>
                </dd>
        </dl>
</div>
<div class="bottomFix">
<?php if(empty($apply)&&$course['isapply_open']==1&&(strtotime($course['apply_start']) < time())&&(strtotime($course['apply_end']) > time())&&($course['apply_num']==0||$course['apply_count']<$course['apply_num'])){ ?>
    <a href="<?php echo site_url('course/apply/'.$course['id']) ?>" class="blueBtnH40" >我要报名</a>
<?php }elseif(strtotime($course['apply_end']) < time()|| strtotime($course['time_start']) < time() || ($course['apply_num']>0&&$c['apply_count']>=$course['apply_num'])){ ?>
    <span class="blueBtnH40 bgBlack" >报名已结束</span>
<?php }elseif($course['isapply_open']==1&&$apply['status']==3){ ?>
    <span class="blueBtnH40 bgBlack" >报名待审核</span>
<?php }else{ ?>
    <span class="blueBtnH40 bgBlack" >报名未开始</span>
<?php } ?>
</div>
