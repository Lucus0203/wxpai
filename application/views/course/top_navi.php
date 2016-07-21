<ul class="topNavi">
    <li <?php if(strpos(current_url(),'course/applyinfo')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/applyinfo/'.$course['id']) ?>">课程概况</a></li>
    <li <?php if(strpos(current_url(),'course/survey')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/survey/'.$course['id']) ?>">课前调研</a></li>
    <li <?php if(strpos(current_url(),'course/homework')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/homework/'.$course['id']) ?>">课前作业</a></li>
    <li <?php if(strpos(current_url(),'course/ratings')){?> class="cur"<?php } ?>><a href="<?php echo site_url('course/ratings/'.$course['id']) ?>">课程反馈</a></li>
</ul>