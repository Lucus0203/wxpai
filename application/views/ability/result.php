<!--head-->
<script type="text/javascript" src="<?php echo base_url(); ?>js/Chart.bundle.min.js"></script>
<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
    }
</style>
<script>
    $(function(){
        var config = {
            type: 'radar',
            data: {
                labels: [<?php if(array_key_exists(1,$abilities)){?>"专业/技能",<?php } ?> <?php if(array_key_exists(3,$abilities)){?>"领导力",<?php } ?><?php if(array_key_exists(5,$abilities)){?>"经验",<?php } ?><?php if(array_key_exists(4,$abilities)){?>"个性",<?php } ?><?php if(array_key_exists(2,$abilities)){?>"通用",<?php } ?>],
                datasets: [{
                    label:'<?php echo $loginInfo['name'] ?>',
                    backgroundColor: "rgba(255, 206, 73,0.5)",
                    pointBackgroundColor: "rgba(217, 163, 73,1)",
                    data: [<?php if(array_key_exists(1,$abilities)){ echo round($abilities[1]['point']/$abilities[1]['level']*5,1) ?>,<?php } ?>
                        <?php if(array_key_exists(3,$abilities)){ echo round($abilities[3]['point']/$abilities[3]['level']*5 ,1)?>,<?php } ?>

                        <?php if(array_key_exists(5,$abilities)){ echo round($abilities[5]['point']/$abilities[5]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(4,$abilities)){ echo round($abilities[4]['point']/$abilities[4]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(2,$abilities)){ echo round($abilities[2]['point']/$abilities[2]['level']*5,1) ?>,<?php } ?>]
                },{
                    label:'<?php echo $abilityjob['name'] ?>',
                    backgroundColor: "rgba(156,224,234,0.5)",
                    pointBackgroundColor: "rgba(220,220,220,1)",
                    data: [<?php if(array_key_exists(1,$abilities)){ echo round($standard[1]/$abilities[1]['level']*5,1) ?>,<?php } ?>
                        <?php if(array_key_exists(3,$abilities)){ echo round($standard[3]/$abilities[3]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(5,$abilities)){ echo round($standard[5]/$abilities[5]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(4,$abilities)){ echo round($standard[4]/$abilities[4]['level']*5,1) ?>,<?php } ?>

                        <?php if(array_key_exists(2,$abilities)){ echo round($standard[2]/$abilities[2]['level']*5,1) ?>,<?php } ?>]
                },]
            },
            options: {
                legend: {
                    //display:false
                    labels:{padding:15}
                },
                scale: {
                    gridLines: {
                        color: ['#d8d8d8']
                    },
                    ticks: {
                        beginAtZero: true
                    }
                },
                responsive: true
            }
        };
        window.myRadar = new Chart(document.getElementById("canvas"), config);
        $(window).resize(function(){
            if($(window).height()>$(window).width()){
                $('#canvas-wrap').width('200%');
            }else{
                $('#canvas-wrap').width('100%');
            }
            var left=($('#canvas-wrap').width()-$('.pinggu').width())/2;
            left=left<0?left:left*-1;
            $('#canvas-wrap').css('margin-left',left+'px');
            window.myRadar.resize();
        });
        $(window).resize();
    });
</script>
<header class="clearfix mb0" id="gHeader">
    <div class="header">
        <a href="<?php echo site_url('ability/index') ?>"><i class="iright">◇</i></a><?php echo $abilityjob['name'] ?>评估结果
        <a href="#"><i class="ilevel">=</i></a>
    </div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <div class="pinggu">
        <p class="aCenter f24 mb20">恭喜你，评估完成</p>
        <p class="gray9 f16 aCenter">自我评估结果</p>
        <div id="canvas-wrap" style="width:100%;">
            <canvas id="canvas"></canvas>
        </div>
    </div>
    <div class="bottom">

        <a href="<?php echo site_url('ability/resultdetail/'.$abilityjob['id']) ?>" class="gray3 f18">查看详情<br><span class="iconV"><img src="<?php echo base_url() ?>images/icon2.png" alt=""></span></a>

    </div>

</div>