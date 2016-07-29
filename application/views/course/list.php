<script type="text/javascript">
$(document).ready(function(){
    $('#more').click(function(){
        var num=$('#current_num').val();
        $.ajax({
                type:"post",
                url:'<?php echo site_url('course/morecourse') ?>',
                data:{'num':num},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var count=0;
                    var str='';
                    $.each(json_obj,function(i,item){
                        str+='<div class="listCont">'+
                            '<div class="imgBox"><a href="<?php echo base_url() ?>course/info/'+item.id+'"><img src="<?php echo $this->config->item('pc_url').'uploads/course_img/' ?>'+item.page_img+'" alt="" width="160"></a></div>'+
                            '<div class="listText">'+
                                    '<p class="titp"><a href="<?php echo base_url() ?>course/info/'+item.id+'">'+item.title+'</a>';
                            if(item.status==1){
                                if(item.apply_num>0&&item.apply_count>=item.apply_num){
                                    str+='<span class="redH25">报名满额</span>';
                                }else{
                                    str+='<span class="greenH25">报名中</span>';
                                }
                            }else if(item.status==3){
                                str+='<span class="greenH25">进行中</span>';
                            }else if(item.status==4){
                                str+='<span class="grayH25">已结束</span>';
                            }else{
                                str+='<span class="orangeH25">报名未开启</span>';
                            }
                            if(item.apply_status==3){
                                str+='<span class="orangeH25">待审核</span>';
                            }else if(item.apply_status==2){
                                str+='<span class="redH25">报名被拒</span>';
                            }else if(item.apply_status==1){
                                str+='<span class="greenH25">报名成功</span>';
                            }
                        str+='</p>';
                        if($.trim(item.teacher)!=''){
                               str+='<p>课程讲师：<span class="blue">'+item.teacher+'</span> </p>';
                           }
                        str+='<p><span class="mr30">开课时间：'+item.time_start+'</span> </p>'+
                            '<p>开课地点：'+item.address+'</p></div></div>';
                        ++count;
                    });
                    $('.listBox').append(str);
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
<header class="clearfix" id="gHeader">
        <div class="header">内训课程<a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts">
            <div class="listBox">
                <input id="total" type="hidden" value="<?php echo $total ?>" autocomplete="off" />
                <input id="current_num" type="hidden" value="<?php echo $current_num ?>" autocomplete="off" />
                <?php if(count($courses)<=0){ ?><p class="titp">暂未发布课程</p><?php } ?>
                <?php foreach ($courses as $c){?>
                    <div class="listCont">

                        <div class="imgBox"><a href="<?php echo site_url('course/info/'.$c['id']) ?>"><img src="<?php echo empty($c['page_img'])?$this->config->item('pc_url').'images/course_default_img.jpg':$this->config->item('pc_url').'uploads/course_img/'.$c['page_img'] ?>" alt="" width="160"></a></div>
                            <div class="listText">
                <p class="titp"><a href="<?php echo site_url('course/info/'.$c['id']) ?>"><?php echo $c['title'] ?></a>
                    <?php if($c['status']==1){//1报名中3进行中4结束2待开启报名9其他 ?>
                        <?php if($c['apply_status']==3){ ?><span class="orangeH25">待审核</span>
                        <?php }elseif($c['apply_status']==2){ ?><span class="redH25">报名被拒</span>
                        <?php }elseif($c['apply_status']==1){ ?><span class="greenH25">报名成功</span>
                        <?php }elseif($c['apply_num']>0&&$c['apply_count']>=$c['apply_num']){ ?><span class="redH25">报名满额</span>
                        <?php }else{ ?><span class="greenH25">报名中</span><?php } ?>
                    <?php }elseif($c['status']==3){ ?>
                        <?php if($c['apply_status']==3){ ?><span class="orangeH25">待审核</span>
                        <?php }elseif($c['apply_status']==2){ ?><span class="redH25">报名被拒</span>
                        <?php }else{ ?><span class="greenH25">进行中</span><?php } ?>
                    <?php }elseif($c['status']==4){ ?>
                        <span class="grayH25">已结束</span>
                    <?php }else{ ?>
                        <span class="orangeH25">报名未开启</span>
                    <?php } ?>
                                
                    
                                    </p>
                                <?php if(!empty($c['teacher'])){ ?>
                                    <p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/info/'.$c['teacher_id']) ?>"><?php echo $c['teacher'] ?></a> </p>
                                <?php } ?>
                                    <p><span class="mr30">开课时间：<?php echo date("Y-m-d H:i",  strtotime($c['time_start'])) ?></span> </p>
                                    <p>开课地点：<?php echo $c['address'] ?></p>
                            </div>
                    </div>
                <?php } ?>
            </div>
    <?php if($total>$current_num){ ?>
            <a id="more" href="javascript:void(0);" class="loadText">加载更多...</a>
    <?php } ?>
    </div>

