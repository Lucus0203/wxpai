<div class="rightBarBox" style="display: none;">
        <div class="mapBg"></div>
        <div class="rightBar">
                <ul class="rightBarList">
                        <li><span class="fLeft"><?php echo $loginInfo['name'] ?></span><a href="<?php echo base_url('login/loginout') ?>" class="fRight">退出登录</a></li>
                        <li><a href="<?php echo base_url('course/index') ?>">内训课程</a></li>
                        <li><a href="<?php echo base_url('course/mycourses') ?>">我报名的课程</a></li>
                        <li><a href="javascript:void(0);">系统消息<i class="rdedian"></i></a></li>
                        <li><a href="<?php echo site_url('center/info') ?>">个人资料</a></li>
                        <li><a href="<?php echo site_url('center/changepass') ?>">密码修改</a></li>
                </ul>
        </div>
</div>