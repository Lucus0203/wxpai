<!--head-->
<script>
    var currentPage=0;
    $(function(){
        $('ul.star li').click(function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur').siblings().removeClass('cur');
            $(this).parent().parent().find('.starTxt').hide().eq(i).show();
            $(this).parent().parent().prev().val((i+1));
            return false;
        });
        $('.next').click(function(){
            //if(confirm('确认下一页吗?')){
                currentPage++;
                $('.abilityBox').eq(currentPage).show().siblings().hide();
                $('body,html').scrollTop(0);
            //}
        });
        $('.abilityBox:gt(0)').hide();
    });
</script>
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <?php echo $abilityjob['name']?>能力评估
        <a href="javascript:void(0);"><i class="ilevel">=</i></a>
    </div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <form method="post" action="<?php echo site_url('ability/evaluatestore') ?>">
        <input type="hidden" name="abilityjob_id" value="<?php echo $abilityjob['id']?>" />
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
                <dt><?php echo ($k+1).'、'.$a['name'] ?></dt>
                <dd <?php if($a===end($abilies)){echo 'class="noborder"';}?> ><p class="txtIn"><?php echo $a['info'] ?></p>
                    <input type="hidden" name="modid[<?php echo $a['id']?>]" value="1" />
                    <div class="starBox">
                        <ul class="star">
                            <?php for($i=1;$i<=$a['level'];$i++){?>
                                <li <?php if($i==1){ ?>class="cur"<?php } ?>>
                                    <a href="#"><?php echo $i ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php for($i=1;$i<=$a['level'];$i++){?>
                            <p class="starTxt" <?php if($i>1){ ?>style="display: none;"<?php } ?> ><?php echo nl2br($a['level_info'.$i]) ?></p>
                        <?php } ?>
                    </div>
                </dd>
            <?php } ?>
        </dl>
        <div class="bottom">
            <p class="aCenter gray9"><?php echo ($page++).'/'.count($abilities) ?></p>
            <?php if($abilies===end($abilities)) { ?>
                <input type="submit" value="确认提交" class="blueBtnH40">
            <?php }else{ ?>
                <input type="button" value="下一页  >" class="blueBtnH40 next">
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    </form>


</div>