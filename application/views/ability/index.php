<script type="text/javascript">
    $(document).ready(function(){
        $('#more').click(function(){
            var num=$('#current_num').val();
            $.ajax({
                type:"post",
                url:'<?php echo site_url('ability/more/') ?>',
                data:{'num':num},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var count=0;
                    var str='';
                    $.each(json_obj,function(i,item){
                        str+='<dt>'+item.name+'</dt>'+
                            '<dd><p class="fLeft">岗位职级：'+item.level+'<br>'+
                        '结束时间：'+item.time_end+'<br>'+
                        '评估状态：';
                        if(item.status==2){
                            str+='<span class="green">已完成</span>';
                        }else if(item.status==1){
                            str+='<span class="red">未评估</span>';
                        }
                        str+='</p><p class="fRight pt10">';
                        if(item.status==2){
                            str+='<a href="<?php echo base_url() ?>ability/result/'+item.id+'" class="borBlue">查看结果</a>';
                        }else if(item.status==1){
                            str+='<a href="<?php echo base_url() ?>ability/assess/'+item.id+'" class="borBlue">开始评估</a>';
                        }
                        str+='</p></dd>';
                        ++count;
                    });
                    $('.caiwuList').append(str);
                    var current_num=$('#current_num').val()*1+count;
                    $('#current_num').val(current_num);
                    if(current_num>=$('#total').val()*1){
                        $('#more').remove();
                    }
                }
            });
        });
    });
</script>
<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header">能力评估</div>
</header>
<div class="mConts">
    <dl class="caiwuList">
        <input id="total" type="hidden" value="<?php echo $total ?>" autocomplete="off" />
        <input id="current_num" type="hidden" value="<?php echo $current_num ?>" autocomplete="off" />
        <?php if(count($jobs)>0){ ?>
            <?php foreach ($jobs as $c){ ?>
                <dt><?php echo $c['name'] ?></dt>
                <dd>
                    <p class="fLeft">岗位职级：<?php echo $c['level'] ?><br>
                        结束时间：<?php echo date("m-d H:i",strtotime($c['time_end'])) ?><br>
                        评估状态：<?php echo ($c['status']=='2')?'<span class="green mr10">已完成</span>':'<span class="red mr10">未评估</span>'; ?></p>
                    <p class="fRight pt10"><?php echo ($c['status']=='2')?'<a href="'. site_url('ability/result/'.$c['id']) .'" class="borBlue">查看结果</a>':'<a href="'. site_url('ability/assess/'.$c['id']) .'" class="borBlue">开始评估</a>'; ?></p>
                </dd>
            <?php } ?>
        <?php }else{ ?>
            <p class="tipsF14">暂未发布能力评估。</p>
        <?php } ?>
    </dl>
    <?php if($total>$current_num){ ?>
        <a id="more" href="javascript:void(0);" class="loadText">加载更多...</a>
    <?php } ?>
</div>

