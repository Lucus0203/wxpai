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
                    label:'<?php echo $student['name'] ?>',
                    backgroundColor: "rgba(255, 206, 73,0.5)",
                    pointBackgroundColor: "rgba(255, 206, 73,1)",
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
        <a href="<?php echo site_url('abilitymanage/staffevaluation/'.$evaluation['id']) ?>"><i class="iright">◇</i></a><?php echo $abilityjob['name'] ?>评估结果
    </div>
</header>
<div class="mConts p0" style="overflow: hidden;">
    <div class="pinggu">
        <p class="aCenter f24 mb20"><?php echo $student['name']?><?php echo ($isother)?'他评结果':'自评结果' ?></p>
        <div id="canvas-wrap" style="width:100%;">
            <canvas id="canvas"></canvas>
        </div>
    </div>
    <div class="bottom">

        <a href="<?php echo ($isother)?site_url('abilitymanage/resultdetailother/'.$evaluation['id'].'/'.$student['id']):site_url('abilitymanage/resultdetail/'.$evaluation['id'].'/'.$student['id']) ?>" class="gray3 f18">查看详情<br><i class="fa fa-chevron-down fa-lg gray9"></i></a>

    </div>

</div>