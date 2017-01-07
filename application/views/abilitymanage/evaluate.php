<!--head-->
<script>
    var currentPage=0;
    $(function(){
        $('ul.star li').click(function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur yellow').siblings().removeClass('cur yellow');
            $(this).parent().parent().find('.starTxt').hide().eq(i).show();
            $(this).parent().parent().prev().val((i+1));
            return false;
        });
        $('.next').click(function(){
            if($('.abilityBox').eq(currentPage).find('ul.star').length>$('.abilityBox').eq(currentPage).find('li.yellow').length) {
                alert('还有能力没有进行评估');
            }else{
                currentPage++;
                $('.abilityBox').eq(currentPage).show().siblings().hide();
                $('body,html').scrollTop(0);
            }
        });
        $('.abilityBox:gt(0)').hide();
    });
</script>
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <a href="<?php echo site_url('abilitymanage/staffevaluation/'.$evaluation['id']) ?>"><i class="iright">◇</i></a><?php echo $abilityjob['name']?>能力评估
    </div>
</header>
<div class="mConts p0 mb0">
    <form method="post" action="<?php echo site_url('abilitymanage/evaluatestore') ?>">
        <input type="hidden" name="evaluation_id" value="<?php echo $evaluation['id']?>" />
        <input type="hidden" name="student_id" value="<?php echo $student['id']?>" />
    <?php $page=1; foreach ($abilities as $key=>$abilies) { ?>
    <div class="abilityBox">
        <dl class="kecDl aCenter">
            <dt>
            <?php if($key==1){
                echo '一、专业/技能';
            }elseif($key==2){
                echo '二、通用能力';
            }elseif($key==3){
                echo '三、领导力';
            }elseif($key==4){
                echo '四、个性';
            }elseif($key==5){
                echo '五、经验';
            } ?>
            </dt>
        </dl>
        <dl class="kecDl">
            <?php foreach ($abilies as $k=>$a){ ?>
                <dt><?php echo ($k+1).'、'.$a['name'] ?><input type="hidden" name="modname[<?php echo $a['id']?>]" value="<?php echo $a['name']?>" /></dt>
                <dd <?php if($a===end($abilies)){echo 'class="noborder"';}?> ><p class="txtIn"><?php echo $a['info'] ?></p>
                    <input type="hidden" name="modid[<?php echo $a['id']?>]" value="1" />
                    <div class="starBox mt10">
                        <ul class="star">
                            <?php for($i=1;$i<=$a['level'];$i++){?>
                                <li>
                                    <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php for($i=1;$i<=$a['level'];$i++){?>
                            <p class="starTxt" style="display: none;"><?php echo nl2br($a['level_info'.$i]) ?></p>
                        <?php } ?>
                    </div>
                </dd>
            <?php } ?>
        </dl>
        <div class="bottom">
            <p class="aCenter gray9"><?php echo ($page++).'/'.count($abilities) ?></p>
            <?php if($abilies===end($abilities)) { ?>
                <input type="submit" value="确认提交" class="blueBtnH40 mb0">
            <?php }else{ ?>
                <input type="button" value="下一页  >" class="blueBtnH40 mb0 next">
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    </form>


</div>