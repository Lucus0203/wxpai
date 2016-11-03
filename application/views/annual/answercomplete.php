<script>
    $(document).ready(function() {
        $('.courseChoice').click(function(){
            if($(this).find('input').is(':disabled')){
                $(this).parent().addClass('on').find('input').removeAttr('disabled');
            }else{
                $(this).parent().removeClass('on').find('input').attr('disabled','disabled');
            }
            return false;
        });
    });
</script>
<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><a href="<?php echo site_url('mission/index');?>"><i class="iright">◇</i></a><?php echo !empty($err)?'暂无问卷':$survey['title'] ?></div>
</header>
<div class="mConts p0">
    <div>
        <?php if($err=='above'){ ?>
            <p class="tipsF14">调研问卷提交名额超过5名,请联系您的培训老师。</p>
        <?php }elseif($err=='noexist'){ ?>
            <p class="tipsF14">调研问卷未发布或已过期。</p>
        <?php }else{ ?>
            <p class="tipsF14">调研问卷已完成,感谢您的提交。</p>
        <?php } ?>
    </div>
</div>