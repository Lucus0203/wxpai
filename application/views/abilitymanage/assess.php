<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <a href="<?php echo site_url('abilitymanage/staffevaluation/'.$evaluation['id']) ?>"><i class="iright">◇</i></a><?php echo $student['name']?>能力评估
    </div>
</header>
<div class="mConts p0 clearfix">
    <div class="pinggu">
        <p class="aCenter f24 mb20"><?php echo $abilityjob['name'] ?></p>
        <p>能力评估能够帮助员工更好的了解自己的职业能力，发现欠缺的技能或经验</p>
        <ul class="pinggulist">
            <?php foreach ($countarry as $a){ ?>
                <?php if($a['type']==1){
                    echo '<li><span class="f50 gray9 verAlignDot">&middot;</span>专业/技能评估('.$a['num'].')</li>';
                }elseif($a['type']==2){
                    echo '<li><span class="f50 gray9 verAlignDot">&middot;</span>通用能力评估('.$a['num'].')</li>';
                }elseif($a['type']==3&&$abilityjob['hasleadership']==1){
                    echo '<li><span class="f50 gray9 verAlignDot">&middot;</span>领导力评估('.$a['num'].')</li>';
                }elseif($a['type']==4){
                    echo '<li><span class="f50 gray9 verAlignDot">&middot;</span>个性评估('.$a['num'].')</li>';
                }elseif($a['type']==5){
                    echo '<li><span class="f50 gray9 verAlignDot">&middot;</span>经验评估('.$a['num'].')</li>';
                } ?>
            <?php } ?>
        </ul>
    </div>
    <div class="bottomFix">

        <a href="<?php echo site_url('abilitymanage/evaluate/'.$evaluation['id'].'/'.$student['id']) ?>" class="blueBtnH40">开始评估</a>

    </div>

</div>