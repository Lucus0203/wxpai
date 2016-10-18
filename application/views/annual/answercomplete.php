<script>
    $(document).ready(function() {
        $('.courseChoice').click(function(){
            if($(this).find('input').is(':disabled')){
                $(this).parent().addClass('on').find('input').removeAttr('disabled');
            }else{
                $(this).parent().removeClass('on').find('input').attr('disabled','disabled');
            }
            return false;
        });
    });
</script>
<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><?php echo $survey['title'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <div>
        <p class="tipsF14">调研问卷已完成,感谢您的提交。</p>
    </div>
</div>