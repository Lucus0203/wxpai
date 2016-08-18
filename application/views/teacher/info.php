<!--head-->
<header class="clearfix mb0" id="gHeader">
        <div class="header"><a href="<?php echo $_SERVER['HTTP_REFERER'];?>"><i class="iright">◇</i></a><?php echo $teacher['name'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="proCont">
                <div class="imgBox">
                        <img src="<?php echo !empty($teacher['head_img'])?$this->config->item('pc_url').'uploads/teacher_img/'.$teacher['head_img']:$this->config->item('pc_url').'images/face_default.png' ?>" alt="" width="160">
                </div>

                <div class="listText">
                    <p>师资类型：<?php echo $teacher['type']==2?'外部':'内部' ?></p>
                    <?php echo (!empty($teacher['title']))?'<p>讲师头衔：'.$teacher['title'].'</p>':''; ?>
                    <?php echo (!empty($teacher['specialty']))?'<p>擅长类别：'.$teacher['specialty'].'</p>':''; ?>
                    <?php echo (!empty($teacher['years']))?'<p>授课年限：'.$teacher['years'].'</p>':''; ?>
                    <p>工作形式：<?php echo $teacher['work_type']==2?'兼职':'专职' ?></p>
                </div>
        </div>
<div class="mConts">

        <dl class="kecDl">
                <?php if(!empty($teacher['info'])){ ?>
                <dt>讲师介绍</dt>
                <dd class="noborder">
                        <?php echo nl2br($teacher['info']) ?>
                </dd>
                <?php } ?>
        </dl>

</div>