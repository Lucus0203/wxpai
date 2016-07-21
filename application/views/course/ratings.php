<script type="text/javascript">
        $(document).ready(function(){
                $('.starsList a').click(function(){
                    var i=$('.starsList a').index($(this));
                    $('.starsList a').removeClass('starOn');
                    $('.starsList a :lt('+i+')').addClass('starOn');
                    $(this).addClass('starOn');
                    $('.star').val($('.starsList a.starOn').length);
                });
                $('[js="pingFen"] a').click(function(){
                    $(this).parent().siblings().find('a').css({'background-color':'#fff','color':'#7f7f7f'});
                    $(this).css({'background-color':'#00bbd3','color':'#fff'});
                    $(this).parent().parent().parent().prev().find('.star').val($(this).attr('rel'));
                    return false;
                })
        })
</script>
<!--head-->
    <header class="clearfix mb0" id="gHeader">
            <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
            <?php $this->load->view ( 'rightbar' ); ?>
    </header>
    <div class="mConts p0">
            <?php $this->load->view('course/top_navi') ?>

        <?php if(!empty($question)){ ?>
            <p class="red"><?php echo $msg ?></p>
            <form method="post" action="">
            <div class="startConts">
                    <input name="qid[]" type="hidden" value="<?php echo $question[0]['id'] ?>" />
                    <input class="star" name="star[]" type="hidden" value="<?php echo $question[0]['star'] ?>" /><input name="content[]" type="hidden" value="" />
                    <p class="aCenter f18">您对本次课程的总体评价是</p>
                    <div class="starsList"><a href="javascript:;" <?php if($question[0]['star']>=1){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=2){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=3){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=4){?> class="starOn"<?php } ?>>★</a><a href="javascript:;" <?php if($question[0]['star']>=5){?> class="starOn"<?php } ?>>★</a></div>
                    <p class="aCenter gray9">轻点星星来评分</p>
            </div>
            <p class="p20 aLeft">
                    <span class="f18 fblock mb10">请继续完成以下反馈</span>
                    <span class="gray9">(很满意5分，满意4分，一般3分，不满意2分，很不满意1分)</span>

            </p>
                <input type="hidden" name="act" value="act" />
            <dl class="fankui">
                <?php foreach ($question as $k=>$q){ if($k>0){ ?>
                    <?php if($q['type']==1){ ?>
                <dt><?php echo $q['title']?>
                    <input name="qid[]" type="hidden" value="<?php echo $q['id'] ?>" />
                    <input class="star" name="star[]" type="hidden" value="<?php echo $q['star'] ?>" /><input name="content[]" type="hidden" value="" /></dt>
                    <dd>
                            <ul js="pingFen">
                                <li><a rel="5" <?php if($q['star']==5){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">5分</a></li>
                                    <li><a rel="4" <?php if($q['star']==4){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">4分</a></li>
                                    <li><a rel="3" <?php if($q['star']==3){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">3分</a></li>
                                    <li><a rel="2" <?php if($q['star']==2){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">2分</a></li>
                                    <li><a rel="1" <?php if($q['star']==1){ ?>style="background-color:#00bbd3;color:#fff"<?php } ?> href="javascript:void(0);">1分</a></li>
                            </ul>
                    </dd>
                    <?php }elseif($q['type']==2){ ?>
                    <dt><?php echo $q['title']?>
                        <input name="qid[]" type="hidden" value="<?php echo $q['id'] ?>" />
                        <input name="star[]" type="hidden" value="" /></dt>
                    <dd>
                        <textarea name="content[]" class="areaH150" placeholder="您的意见或建议"><?php echo $q['content'] ?></textarea>
                    </dd>
                    <?php } ?>
                <?php }} ?>
            </dl>
            <ul class="btnList">
                <li class="wb70"><input type="submit" value="提交评价" class=" blueBtnH40" /></li>

            </ul>
            </form>
        <?php }else{ ?>
            <div class="startConts">
                <p class="aCenter f18">本课程暂时无需反馈</p>
            </div>
        <?php } ?>
    </div>