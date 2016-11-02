<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <a href="<?php echo site_url('ability/index') ?>"><i class="iright">◇</i></a>能力评估
    </div>
</header>
<div class="mConts p0">
    <div class="pinggu">
        <p class="aCenter f24 mb20"><?php echo $abilityjob['name'] ?></p>
        <p>能力评估能够帮助你更好的了解自己的职业能力，发现自己欠缺的技能或经验</p>
        <ul class="pinggulist">
            <?php foreach ($countarry as $a){ ?>
            <li><span class="f50 gray9 verAlignDot">&middot;</span>
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
                } ?>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="bottomFix">

        <a href="<?php echo site_url('ability/evaluate/'.$abilityjob['id']) ?>" class="blueBtnH40">开始评估</a>

    </div>

</div>