<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header">事项</div>
</header>
<div class="mConts p0">
    <ul class="ulList">
        <li><a href="<?php echo site_url('ability/index') ?>">能力评估</a></li>
        <li><a href="<?php echo ($annualSurveyStatus>0)?site_url('annual/answer'):'#' ?>">年度需求调研<span class="orange f14 ml10"><?php if($annualSurveyStatus<=0){echo '暂无';}else{echo ($annualSurveyStatus==2)?'已完成':'待完成';} ?></span></a></li>
    </ul>
</div>