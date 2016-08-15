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
                <?php }else{ $siginInBtnflag=1; ?>
                    <a href="javascript:void(0)" class="grayH42 bgOrange fLeft">待签到</a>
                <?php }} ?>
        </div>
        <div class="listText">
            <?php if(!empty($teacher['name'])){?>
                <p>课程讲师：<a class="blue" href="<?php echo site_url('teacher/info/'.$teacher['id']);?>"><?php echo $teacher['name'] ?></a> </p>
            <?php } ?>
            <p>开始时间：<?php echo date("m-d H:i",  strtotime($course['time_start'])) ?></p>
            <p>结束时间：<?php echo date("m-d H:i",  strtotime($course['time_end'])) ?></p>
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

<?php if(!empty($siginInBtnflag)&&strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){ ?>
    <div class="bottomFix"><a href="javascript:void(0)" id="siginInBtn" class="blueBtnH40" >我要签到</a></div>
<?php } ?>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: ['scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
    });
    wx.ready(function(){
        $('#siginInBtn').click(function(){
            wx.scanQRCode({
                needResult: 0, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
                success: function (res) {
                }
            });
        });
    });
</script>