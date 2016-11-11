<script>
    $(document).ready(function() {
        $('.approvedBtn').click(function(){
            var course=$(this).attr('rel');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('annualmanage/approvedcourse/'.$plan['id'].'/'.$loginInfo['id']) ?>',
                data:{'course':course},
                async: false,
                success: function (res) {
                    if(res==1){
                        $('a[rel='+course+']').eq(0).hide().next().show()
                            .parent().prev().find('span').text('已通过').addClass('green').removeClass('red');
                    }
                }
            });
            return false;
        });
        $('.unapprovedBtn').click(function(){
            var course=$(this).attr('rel');
            $.ajax({
                type: "post",
                url: '<?php echo site_url('annualmanage/unapprovedcourse/'.$plan['id'].'/'.$loginInfo['id']) ?>',
                data:{'course':course},
                async: false,
                success: function (res) {
                    if(res==1){
                        $('a[rel='+course+']').eq(1).hide().prev().show()
                            .parent().prev().find('span').text('未通过').addClass('red').removeClass('green');
                    }
                }
            });
            return false;
        });
    });
</script>
<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><a href="<?php echo site_url('annualmanage/approved') ?>"><i class="iright">◇</i></a><?php echo $student['name'] ?></div>
</header>
<div class="mConts">

    <dl class="caiwuList">
        <?php foreach ($courses as $c){ ?>
        <dt><?php echo $c['title'] ?></dt>
        <dd>
            <p class="fLeft">课程预算：<?php echo round($c['price']/$c['people']) ?>元/人<br>
                审核状态：<?php if(empty($c['status'])){?><span class="yellow1 mr10">未审核</span><?php }else{echo ($c['status']=='1')?'<span class="green mr10">已通过</span>':'<span class="red mr10">未通过</span>';} ?></p>
            <p class="fRight pt10"><a href="#" rel="<?php echo $c['annual_course_id'] ?>" <?php if($c['status']=='1'){?>style="display: none"<?php } ?> class="borBlue approvedBtn ">通过</a><a href="#" rel="<?php echo $c['annual_course_id'] ?>" <?php if($c['status']=='2'){?>style="display: none"<?php } ?> class="borBlue unapprovedBtn ml10">不通过</a></p>
        </dd>
        <?php } ?>
    </dl>
</div>