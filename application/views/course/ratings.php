<script type="text/javascript">
    $(document).ready(function(){
        $('.starsList a').click(function(){
            var i=$('.starsList a').index($(this));
            $('.starsList a').removeClass('starOn');
            $('.starsList a :lt('+i+')').addClass('starOn');
            $(this).addClass('starOn');
            $('.totalStar').val($('.starsList a.starOn').length);
            $('.msg1th').hide();
        });
        $('[js="pingFen"] a').click(function(){
            $(this).parent().siblings().find('a').css({'background-color':'#fff','color':'#7f7f7f'});
            $(this).css({'background-color':'#00bbd3','color':'#fff'});
            $(this).parent().parent().prev().prev().val($(this).attr('rel'));
            $(this).parent().parent().parent().prev().hide();
            return false;
        });
    });
    function checkForm(){
        var flag=true;
        var top=0;
        if($('.totalStar').val()==''){
            flag=false;
            top=$('.startConts').offset().top;
            $('.msg1th').show();
        }else{
            $('.msg1th').hide();
        }
        var starflag=true;
        $('.typeVal').each(function(i){
            if($(this).val()==1&&$('.starVal').eq(i).val()==''){
                flag=false;
                starflag=false;
            }
            if($(this).val()==2&&$('.contentVal').eq(i).val()==''){
                flag=false;
                starflag=false;
            }
            if(!starflag){
                $('.msg').eq(i).show();
                top=top==0?$(this).parent().prev().prev().offset().top:top;
            }else{
                $('.msg').eq(i).hide();
            }
            starflag=true;
        });
        if(!flag||!starflag){
            $('html,body').scrollTop(top-50);
        }
        return flag&&starflag;

    }
</script>
<!--head-->
    <header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?></div>
    </header>
    <div class="mConts p0">
        <?php $this->load->view('course/top_navi') ?>

        <?php if(!empty($question)){ ?>
            <p class="red mt10"><?php echo $msg ?></p>
            <form method="post" action="" onsubmit="return checkForm()">
            <div class="startConts">
                    <input name="qid[]" type="hidden" value="<?php echo $question[0]['id'] ?>" />
                    <input class="totalStar" name="star[]" type="hidden" value="<?php echo $question[0]['star'] ?>" /><input name="content[]" type="hidden" value="" />
                    <p class="aCenter f18">您对本次课程的总体评价是</p>
                    <div class="starsList"><a href="javascript:;" <?php if($question[0]['star']>=1){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=2){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=3){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=4){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=5){?> class="starOn"<?php } ?>>★</a></div>
                    <p class="aCenter gray9">轻点星星来评分</p>
                    <p class="aCenter red mt10 msg1th" style="display: none;">请对本次课程评分</p>
            </div>
                <input type="hidden" name="act" value="act" />
            <dl class="fankui">
                <?php foreach ($question as $k=>$q){ if($k>0){ ?>
                    <dt><?php echo $q['title']?></dt>
                    <?php if($q['type']==1){ ?>
                    <p class="red mt10 ml20 msg" style="display: none;">请对本项评分</p>
                    <dd <?php if($q==end($question)){echo 'class="noborder"';}?>>
                        <input class="typeVal" type="hidden" value="<?php echo $q['type'] ?>" />
                        <input name="qid[]" type="hidden" value="<?php echo $q['id'] ?>" />
                        <input class="starVal" name="star[]" type="hidden" value="<?php echo $q['star'] ?>" /><input class="contentVal" name="content[]" type="hidden" value="" />
                        <ul js="pingFen">
                            <li><a rel="1" <?php if($q['star']==1){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">1分</a></li>
                            <li><a rel="2" <?php if($q['star']==2){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">2分</a></li>
                            <li><a rel="3" <?php if($q['star']==3){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">3分</a></li>
                            <li><a rel="4" <?php if($q['star']==4){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">4分</a></li>
                            <li><a rel="5" <?php if($q['star']==5){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">5分</a></li>
                        </ul>
                    </dd>
                    <?php }elseif($q['type']==2){ ?>
                    <p class="red mt10 ml20 msg" style="display: none;">请输入您想说的内容</p>
                    <dd <?php if($q==end($question)){echo 'class="noborder"';}?> >
                        <input class="typeVal" type="hidden" value="<?php echo $q['type'] ?>" />
                        <input name="qid[]" type="hidden" value="<?php echo $q['id'] ?>" />
                        <input class="starVal" name="star[]" type="hidden" value="" />
                        <textarea name="content[]" class="areaH150 contentVal"><?php echo $q['content'] ?></textarea>
                    </dd>
                    <?php } ?>
                <?php }} ?>
            </dl>
                <input type="submit" value="提交评价" class="mb0 blueBtnH40 noradius" />
            </form>
        <?php }else{ ?>
            <div class="startConts">
                <p class="aCenter f18">本课程暂时无需反馈</p>
            </div>
        <?php } ?>
    </div>