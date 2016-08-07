<!--head-->
<header class="clearfix" id="gHeader">
    <div class="header">能力评估<a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts">
    <div class="listBox">
        <?php if(count($jobs)<=0){ ?><p class="titp">暂未发布能力评估</p><?php } ?>
        <?php foreach ($jobs as $c){?>
            <div class="listCont">
                <div class="imgBox"><a href="<?php echo ($c['complate_status']==2)?site_url('ability/result/'.$c['id']):site_url('ability/assess/'.$c['id']) ?>"><img src="<?php echo empty($c['page_img'])?$this->config->item('pc_url').'images/course_default_img.jpg':$this->config->item('pc_url').'uploads/course_img/'.$c['page_img'] ?>" alt="" width="160"></a></div>
                <div class="listText">
                    <p class="titp"><a href="<?php echo ($c['complate_status']==2)?site_url('ability/result/'.$c['id']):site_url('ability/assess/'.$c['id']) ?>"><?php echo $c['name'] ?></a>
                        <?php if($c['complate_status']==1){ ?>
                            <span class="orangeH25">待评估</span>
                        <?php }else{ ?>
                            <span class="greenH25">评估完成</span>
                        <?php } ?>
                    </p>
                    <p>评分：<?php echo !empty($c['point'])?$c['point']:'待评估' ?></p>
                    <p>提交时间：<?php echo ($c['complate_status']==2)?date("Y-m-d H:i",strtotime($c['complate_time'])):'未提交' ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

