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
    function checkRequired(){
        var flag=true;
        $('.required').each(function (i) {
            if($(this).next().find('input:checked').length<=0&&$.trim($(this).next().find('textarea').val())==''){
                flag=false;
            }
        });
        if(!flag){
            alert('您有必答题未答,请您确认并填写');
        }
        return flag;
    }
</script>
<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><?php echo $survey['title'] ?></div>
</header>
<div class="mConts p0 clearfix">

    <ul class="topNavi">
        <?php foreach($modeltyps as $m){
            switch ($m['module']){
                case 1:?>
                    <li class="<?php if($qatype=='acceptance'){?>cur<?php } ?>">
                        <a>认同度</a>
                    </li><?php
                    break;
                case 2:?>
                    <li class="<?php if($qatype=='organization'){?>cur<?php } ?>">
                        <a>组织性</a>
                    </li><?php
                    break;
                case 3:?>
                    <li class="<?php if($qatype=='requirement'){?>cur<?php } ?>">
                        <a>需求信息</a>
                    </li><?php
                    break;
                default:
                    break;
            }?>
        <?php } ?>
        <li class="<?php if($qatype=='coursechosen'){?>cur<?php } ?>">
            <a>课程选择</a>
        </li>
    </ul>

    <?php if($qatype!='coursechosen'){?>
    <form method="post" action="<?php echo site_url('annual/storeAnswer/'.$qatype); ?>" onsubmit="return checkRequired()">
    <input type="hidden" name="act" value="act" />
    <dl class="kecDl">
        <?php foreach ($questions as $kq=>$q ){ ?>
            <dt class="<?php if($q['required']==1){echo 'required';} ?>"><?php echo ($kq+1).'.'.$q['title'] ?><?php echo ($q['required']==2)?'(选答)':'(必答)'; ?></dt>
            <dd <?php if($q===end($questions)){ ?>class="noborder" style="margin-bottom: 0;" <?php } ?> >
                <?php if($q['type']==1||$q['type']==2) {?>
                    <ul class="listUl">
                        <?php foreach ($q['options'] as $ko=>$op){?>
                            <li><label><input name="option<?php echo ($q['type']==2)?$q['id'].'[]':$q['id']; ?>" type="<?php echo ($q['type']==1)?'radio':'checkbox';?>" value="<?php echo $op['id']; ?>">&nbsp;<?php echo $op['content'] ?></label></li>
                        <?php } ?>
                    </ul>
                <?php }else{ ?>
                    <textarea name="answer_content<?php echo $q['id']; ?>" class="areaH150"></textarea>
                <?php } ?>
            </dd>
        <?php } ?>
    </dl>
    <ul class="btnList">
        <li class="wb100"><input type="submit" class="blueBtnH40 mb0 noradius" value="下一页 >"></li>
    </ul>
    </form>
    <?php }else{?>
    <form method="post" action="<?php echo site_url('annual/storeAnswer/'.$qatype); ?>">
        <input type="hidden" name="act" value="act" />
        <p class="tipsF14">请挑选出您最希望接受的培训课程。</p>
        <?php foreach ($courses as $courseType){ ?>
        <div class="feiTips"><span><?php echo $courseType['name'] ?></span></div>
        <ul class="feiList">
            <?php foreach ($courseType['courses'] as $c){?>
            <li>
                <a class="courseChoice" href="#"><?php echo $c['title'] ?><input disabled="disabled" type="hidden" name="course[]" value="<?php echo $c['id'] ?>" /> </a>
            </li>
            <?php } ?>
        </ul>
        <?php } ?>
        <p style="padding: 10px;">
            <textarea name="answer_content" placeholder="您还想补充的培训需求" class="areaH150"></textarea>
        </p>
        <ul class="btnList">
            <li class="wb100"><input type="submit" class="blueBtnH40 mb0" value="提交问卷"></li>
        </ul>
    </form>
    <?php } ?>
</div>