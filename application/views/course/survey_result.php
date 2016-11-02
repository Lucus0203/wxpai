<!--head-->
<header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?></div>
</header>
<div class="mConts p0">
        <?php $this->load->view('course/top_navi') ?>
    <div class="startConts">
            <p class="aCenter f18">您本次的课前调研结果是</p>
    </div>
    <dl class="fankui">
        <?php foreach ($survey as $k => $s){ ?>
            <dt><?php echo ($k*1+1) ?>.<?php echo $s['title'] ?></dt>
            <dd>
                <?php echo nl2br($s['content']) ?>
            </dd>
        <?php } ?>
    </dl>
</div>