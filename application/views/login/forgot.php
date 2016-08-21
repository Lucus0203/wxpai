<!DOCTYPE html>
<html><head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>忘记密码</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="Copyright" content="www.trainingpie.com" />
    <link rel="stylesheet" href="<?php echo base_url() ?>css/common.css">
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // 手机号码验证
            jQuery.validator.addMethod("isMobile", function(value, element) {
                var length = value.length;
                var mobile = /^((1[0-9]{2})+\d{8})$/;
                return this.optional(element) || (length == 11 && mobile.test(value));
            }, "请正确填写您的手机号码");
            $( "#signupForm" ).validate( {
                rules: {
                    company_code: {
                        required: true

                    },
                    mobile: {
                        required: true,
                        digits:true,
                        isMobile: true
                    },
                    mobile_code: {
                        required: true,
                        digits:true
                    },
                    user_pass: {
                        required: true,
                        minlength: 5
                    }
                },
                messages: {

                    company_code: {
                        required: "请输入您的公司编号",
                    },
                    mobile: {
                        required: "请输入您的手机号码",
                        digits: "只能输入数字",
                        isMobile: "请输入正确的手机号码",
                    },
                    mobile_code: {
                        required: "请输入短信验证码",
                        digits: "只能输入数字"
                    },
                    user_pass: {
                        required: "请输入密码",
                        minlength: "密码的长度要大于5个字符"
                    }
                },
                errorPlacement: function ( error, element ) {
                    error.addClass( "ui red pointing label transition" );
                    error.insertAfter( element.parent() );
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).parents( ".row" ).addClass( errorClass );
                },
                unhighlight: function (element, errorClass, validClass) {
                    $( element ).parents( ".row" ).removeClass( errorClass );
                }
            });
            $('#get_captcha').click(function(){
                $.ajax({
                    type: "get",
                    url: '<?php echo site_url('login/updateCaptcha') ?>',
                    success: function (res) {
                        $('#get_captcha img').attr('src','<?php echo base_url()?>uploads/captcha/'+res);
                    }
                })
            });
            $('#get_mobile_code').click(function(){
                var mobile=$('#mobile').val();
                var company_code = $('#company_code').val();
                var captcha = $('#captcha').val();
                if ($.trim(company_code)=='') {
                    alert('请输入公司编号！');
                    $('#company_code').focus();
                    return false;
                }
                if ($.trim(mobile)=='') {
                    alert('请输入手机号码！');
                    $('#mobile').focus();
                    return false;
                }
                if ($.trim(captcha)=='') {
                    alert('请输入4位验证码！');
                    $('#captcha').focus();
                    return false;
                }
                if(ismobile(mobile)&&$('#get_mobile_code').attr('rel')<=0){
                    $.ajax({
                        type:"post",
                        url:'<?php echo site_url('login/getcode/forgot') ?>',
                        data:{'mobile':mobile,'captcha':captcha,'company_code':company_code},
                        success:function(res){
                            if(res==1){
                                $('#get_mobile_code').css('color','#ccc').text('重新获取60').attr('rel','60');
                                remainsecondes=60;
                                timing()
                            }else{
                                alert(res);
                            }
                        }
                    })
                }
                return false;

            });
        });
        function timing(){
            if(remainsecondes>0){
                setTimeout(function(){
                    remainsecondes--;
                    $('#get_mobile_code').text('重新获取'+remainsecondes).attr('rel',remainsecondes);
                    timing();
                },1000);
            }else{
                $('#get_mobile_code').css('color','#00bbd3').text('获取短信').attr('rel',0);
                $('#get_captcha').trigger('click');
            }
        }
        function ismobile(mobile) {
            var myreg = /^0?1[0-9]{2}\d{8}$/;
            if (!myreg.test(mobile)) {
                alert('请输入有效的手机号码！');
                $('input [name=mobile]').focus();
                return false;
            }
            return true;
        }
    </script>

</head>

<body>
<article id="container">
    <!--head-->
    <header class="clearfix" id="gHeader">
        <div class="header"><a href="<?php echo site_url('login/index/'.$company['code']) ?>"><i class="iright">◇</i></a>忘记密码</div>
    </header>
    <div class="mConts">
        <p class="red mb20"><?php echo $msg ?></p>
        <form id="signupForm" action="" method="post" >
            <input type="hidden" name="act" value="act" />
            <div class="iptBox" <?php if(!empty($company['code'])){echo 'style="display:none;"';} ?> >
                <input id="company_code" name="company_code" value="<?php echo $company['code'] ?>" type="text" class="ipt" placeholder="公司编号" />
                <p class="gray9  aLeft">公司编号，是您的公司编号，可以从您的培训经理处获得</p>
            </div>
            <?php if(!empty($company['code'])){?>
                <div class="iptBox">
                    <p class="gray9 aCenter f18"><?php echo $company['name'] ?></p>
                </div>
            <?php } ?>
            <div class="iptBox">
                <div class="iptInner">
                    <input id="mobile" name="mobile" value="<?php echo $user['mobile'] ?>" type="text" class="ipt" placeholder="手机号码" />
                </div>
            </div>
            <div class="iptBox">
                <div class="iptInner">
                    <input class="ipt w60 fLeft" type="text" id="captcha" value="" placeholder="4位验证码"/><a id="get_captcha" href="javascript:void(0)" class="captchaBtn blue" rel="0"><img src="<?php echo base_url()?>uploads/captcha/<?php echo $cap['filename'] ?>" /><br>换一个</a>
                </div>
            </div>
            <div class="iptBox">
                <div class="iptInner">
                    <input name="mobile_code" type="text" value="<?php echo $user['mobile_code'] ?>" class="ipt w60 fLeft" placeholder="短信验证码 " autocomplete="off" />
                    <a id="get_mobile_code" href="javascript:void(0);" class="coBtn" rel="0">获取短信</a>
                </div>
            </div>
            <div class="iptBox">
                <input name="user_pass" value="<?php echo $user['user_pass'] ?>" type="text" class="ipt" placeholder="设置密码" autocomplete="off" />
            </div>
            <input type="submit" value="修改密码" class="blueBtnH40">
        </form>
    </div>

</article>

</body>

</html>