<!--head-->
<header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?></div>
</header>
<div class="mConts p0">
        <?php $this->load->view('course/top_navi') ?>
    <?php if(!empty($prepare['note'])||!empty($prepare['file'])) { ?>
        <div class="startConts p20">
        <p class="prepareTxt aLeft f16" style="line-height:26px;"><?php echo nl2br($prepare['note']) ?></p>
        </div>
        <div class="p20">
            <span id="filename" class="mr10"></span><?php if(!empty($prepare['filename'])){ ?><a href="<?php echo site_url('course/preparefile/'.$course['id']) ?>" class="blue" target="_blank">下载文档(<?php echo $prepare['filename'] ?>)</a><?php } ?>
        </div>
    <?php }else{ ?>
        <div class="startConts">
            <p class="aCenter f18">暂无课程公告</p>
        </div>
    <?php } ?>
</div>