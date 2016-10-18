<div class="rightBarBox" style="display: none;">
        <div class="mapBg"></div>
        <div class="rightBar">
                <ul class="rightBarList">
                        <li><span class="fLeft"><?php echo $loginInfo['name'] ?></span><a href="<?php echo base_url('login/loginout') ?>" class="fRight">退出登录</a></li>
                        <li><a href="<?php echo base_url('course/index') ?>">内训课程</a></li>
                        <li><a href="<?php echo base_url('course/index/mycourse') ?>">我的课程</a></li>
                        <li><a href="<?php echo site_url('ability/index') ?>">能力评估</a></li>
                    <?php if($loginInfo['annualSurveyStatus']>0){?>
                        <li><a href="<?php echo site_url('annual/answer') ?>">年度调研<span class="orange f12 ml10"><?php if($loginInfo['annualSurveyStatus']==2){echo '已完成';}else{echo '待完成';} ?></span></a></li>
                    <?php } ?>
                        <li><a href="<?php echo site_url('center/info') ?>">个人资料</a></li>
                        <li><a href="<?php echo site_url('center/changepass') ?>">密码修改</a></li>
                </ul>
        </div>
</div>