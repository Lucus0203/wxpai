<ul class="bottomNavi">
    <li class="<?php if($footerNavi=='course'){?>on<?php } ?>">
        <a href="<?php echo base_url('course/index') ?>"><i class="fa fa-book" aria-hidden="true"></i><span>课程</span></a>
    </li>
    <li class="<?php if($footerNavi=='mission'){?>on<?php } ?>">
        <a href="<?php echo base_url('mission/index') ?>"><i class="fa fa-list-alt" aria-hidden="true"></i><span>事项</span></a>
    </li>
    <li class="<?php if($footerNavi=='center'){?>on<?php } ?>">
        <a href="<?php echo base_url('center/index') ?>"><i class="fa fa-user" aria-hidden="true"></i><span>我的</span></a>
    </li>
</ul>
</article>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?b84ccdce1e3ffd049fdf347fc743928b";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>