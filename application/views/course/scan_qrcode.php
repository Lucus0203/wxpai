<!--head-->
<header class="clearfix mb0" id="gHeader">
    <div class="header"><a href="<?php echo $homeUrl;?>"><i class="iright">◇</i></a><?php echo $course['title'] ?><a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
    <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts p0">
    <?php $this->load->view('course/top_navi') ?>
    <div class="proBox">
        <div class="proInner">
            <div class="img"><img src="<?php echo empty($course['page_img'])?$this->config->item('pc_url').'images/course_default_img.jpg':$this->config->item('pc_url').'uploads/course_img/'.$course['page_img'] ?>" alt="" width="160"></div>
            <?php if(!empty($signindata)){?>
                <a href="javascript:void(0)" class="grayH42 bgGreen fLeft">签到成功</a>
            <?php }else{ ?>
                <?php if($course['issignin_open']!=1||$course['signin_start']>date("Y-m-d H:i:s")){ ?>
                    <a href="javascript:void(0)" class="grayH42 fLeft">签到未开始</a>
                <?php }elseif($course['signin_end']<date("Y-m-d H:i:s")){ ?>
                    <a href="javascript:void(0)" class="grayH42 fLeft">签到已结束</a>
                <?php }else{ ?>
                    <a href="javascript:void(0)" id="siginInBtn" class="grayH42 bgGreen fLeft">我要签到</a>
                <?php }} ?>
        </div>
        <div class="listText">
            <p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/info/'.$teacher['id']);?>"><?php echo $teacher['name'] ?></a> </p>
            <p><span class="mr30">开课时间：<?php echo $course['time_start'] ?></span> </p>
            <p>开课地点：<?php echo $course['address'] ?></p>

        </div>

    </div>

    <dl class="kecDl">
        <?php if(!empty($course['info'])){ ?>
            <dt>课程介绍</dt>
            <dd>
                <?php echo nl2br($course['info']) ?>
            </dd>
        <?php } ?>
        <dt>课程收益</dt>
        <dd><?php echo nl2br($course['income']) ?></dd>
        <dt>课程大纲</dt>
        <dd class="noborder">
            <?php echo nl2br($course['outline']) ?>
        </dd>
    </dl>


</div>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: ['scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
        $('#siginInBtn').click(function(){
            wx.scanQRCode({
                needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                    var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
                    alert(result);
                }
            });
        });
    });
    wx.error(function(res){
        // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
    });
</script>