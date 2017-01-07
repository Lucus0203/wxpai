<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><a href="<?php echo site_url('abilitymanage/index') ?>"><i class="iright">◇</i></a><?php echo $abilityjob['name'] ?></div>
</header>
<div class="mConts">
    <dl class="caiwuList">
        <?php if(count($students)>0){ ?>
            <?php foreach ($students as $s){ ?>
                <dt><?php echo $s['name'] ?></dt>
                <dd>
                    <p class="fLeft">岗位职级：<?php echo $level['name'] ?><br>
                        结束时间：<?php echo date("m-d H:i",strtotime($evaluation['time_end'])) ?><br>
                        自评状态：<?php echo ($s['status']=='2')?'<span class="green mr10">已完成</span>':'<span class="red mr10">未评估</span>'; ?><br>
                        他评状态：<?php echo ($s['others_status']=='2')?'<span class="green mr10">已完成</span>'.'('.$s['others_name'].')':'<span class="red mr10">未评估</span>'; ?>
                    </p>
                    <p class="fRight pt10">
                        <?php if($s['status']=='2'){ ?><a href="<?php echo site_url('abilitymanage/result/'.$s['ability_job_evaluation_id'].'/'.$s['student_id']) ?>" class="borBlue">查看自评</a><?php } ?>
                        <?php if($s['others_status']=='2'){ ?><a href="<?php echo site_url('abilitymanage/resultother/'.$s['ability_job_evaluation_id'].'/'.$s['student_id']) ?>" class="borBlue">查看他评</a><?php }else{?><a href="<?php echo site_url('abilitymanage/assess/'.$s['ability_job_evaluation_id'].'/'.$s['student_id']) ?>" class="borBlue">开始评估</a><?php } ?>
                    </p>
                </dd>
            <?php } ?>
        <?php }else{ ?>
            <p class="tipsF14">暂未符合条件的评估记录。</p>
        <?php } ?>
    </dl>
</div>

