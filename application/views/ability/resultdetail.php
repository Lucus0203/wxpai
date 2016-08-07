<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <?php echo $abilityjob['name'] ?>评估结果
        <a href="#"><i class="ilevel">=</i></a>
    </div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <div class="pinggu">
        <p class="mb20 aCenter"><a href="<?php echo site_url('ability/result/'.$abilityjob['id']) ?>" class="gray3 f18"><span class="iconV"><img src="<?php echo base_url() ?>images/iconUp.png" alt=""></span><br>综合显示</a>
        </p>

        <ul class="pinggulist mb10 w360">
            <?php foreach ($abilities as $key=>$a){ ?>
                <li><span class="txt">
                    <?php if($a['type']==1){
                        echo '专业/技能评估('.$a['num'].')';
                    }elseif($a['type']==2){
                        echo '通用能力评估('.$a['num'].')';
                    }elseif($a['type']==3){
                        echo '领导力评估('.$a['num'].')';
                    }elseif($a['type']==4){
                        echo '个性评估('.$a['num'].')';
                    }elseif($a['type']==5){
                        echo '经验评估('.$a['num'].')';
                    } ?></span>
                    <div class="star">
                        <a href="javascript:;" <?php if($a['point']/$a['level']*5>0){ ?>class="starOn"<?php } ?> >★</a>
                        <a href="javascript:;" <?php if($a['point']/$a['level']*5>1){ ?>class="starOn"<?php } ?>>★</a>
                        <a href="javascript:;" <?php if($a['point']/$a['level']*5>2){ ?>class="starOn"<?php } ?>>★</a>
                        <a href="javascript:;" <?php if($a['point']/$a['level']*5>3){ ?>class="starOn"<?php } ?>>★</a>
                        <a href="javascript:;" <?php if($a['point']/$a['level']*5>4){ ?>class="starOn"<?php } ?>>★</a>
                    </div>
                </li>
            <?php }?>
        </ul>
    </div>


</div>