<!--head-->
<script>
    $(function(){
        $('ul.star li').click(function(){
            var i=$(this).parent().find('li').index($(this));
            $(this).addClass('cur').siblings().removeClass('cur');
            $(this).parent().parent().find('.starTxt').hide().eq(i).show();
            $(this).parent().parent().prev().val((i+1));
            return false;
        });
    });
</script>
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <?php echo $abilityjob['name'] ?>评估结果
        <a href="#"><i class="ilevel">=</i></a>
    </div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <div class="pinggu">
        <p class="mb20 aCenter"><a href="<?php echo site_url('ability/result/'.$abilityjob['id']) ?>" class="gray3 f18"><span class="iconV"><img src="<?php echo base_url() ?>images/iconUp.png" alt=""></span><br>详细显示</a>
        </p>
        <p class="mb20"><i class="fa fa-star fa-lg starColorBlue"></i><?php echo $abilityjob['name'] ?>&nbsp;<i class="fa fa-star fa-lg starColorYellow"></i><?php echo $loginInfo['name'] ?></p>

        <?php foreach ($abilities as $key=>$abilies) { ?>
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
                        <dt><?php echo ($k+1).'、'.$a['name'] ?><input type="hidden" name="modname[<?php echo $a['id']?>]" value="<?php echo $a['model_name']?>" /></dt>
                        <dd <?php if($a===end($abilies)){echo 'class="noborder"';}?> ><p class="txtIn"><?php echo $a['info'] ?></p>
                            <input type="hidden" name="modid[<?php echo $a['id']?>]" value="1" />
                            <div class="starBox mt10">
                                <ul class="star">
                                    <?php for($i=1;$i<=$a['level'];$i++){?>
                                        <li class="<?php if($i==$a['point']){ echo 'cur yellow'; }elseif($a['level_standard']==$i){echo 'blue';} ?>" >
                                            <a href="#"><i class="fa fa-star fa-3x"></i><span class="num"><?php echo $i ?></span></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <?php for($i=1;$i<=$a['level'];$i++){?>
                                    <p class="starTxt" <?php if($i!=$a['level_standard']){ ?>style="display: none;"<?php } ?> ><?php echo nl2br($a['level_info'.$i]) ?></p>
                                <?php } ?>
                            </div>
                        </dd>
                    <?php } ?>
                </dl>
            </div>
        <?php } ?>
    </div>


</div>