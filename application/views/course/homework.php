<!--head-->
<header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?></div>
</header>
<div class="mConts p0">
        <?php $this->load->view('course/top_navi') ?>
    <?php if(!empty($question)) { ?>
        <div class="proBox">
                <p class="aCenter">本课题共有<?php echo $total ?>道课前作业，请完成后提交</p>
        </div>
        <div class="p20">
            <form method="post" action="">
                <input name="act" type="hidden" value="act" />
                <p class="titp"><?php echo $no ?>.<?php echo $question['title'] ?></p>
                <p class="red"><?php echo $msg ?></p>
                <div class="iptBox">
                    <textarea name="content" class="areaH150"><?php echo $answer['content'] ?></textarea>
                </div>
                <ul class="btnList">
                    <?php if($no>1){ ?>
                    <li><a href="<?php echo site_url('course/homework/'.$course['id']) ?>?no=<?php echo $no*1-1 ?>" class="borBlueBtnH40">上一题</a></li>
                    <?php } ?>
                    <?php if($no<$total){ ?>
                    <li><input type="submit" value="下一题" class="borBlueBtnH40" /></li>
                    <?php }else{ ?>
                    <li><input type="submit" value="提交" class="blueBtnH40" /></li>
                    <?php } ?>
                </ul>
            </form>
        </div>
    <?php }else{ ?>
            <div class="startConts">
                <p class="aCenter f18">本课程暂无课前作业</p>
            </div>
    <?php } ?>
</div>