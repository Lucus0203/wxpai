<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<link rel="shortcut icon" href="" type="image/x-icon">
		<link rel="icon" href="" type="image/x-icon">
		<title></title>
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
							required: "请输入您的电话号码",
							digits: "只能输入数字",
							isMobile: "请输入正确的手机号码",
						},
						mobile_code: {
							required: "请输入验证码",
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
                                $('#get_mobile_code').click(function(){
                                    var mobile=$('#mobile').val();
                                    if(ismobile(mobile)&&$('#get_mobile_code').attr('rel')<=0){
                                            $.ajax({
                                                    type:"post",
                                                    url:'<?php echo site_url('login/getcode') ?>',
                                                    data:{'mobile':mobile},
                                                    success:function(res){
                                                            if(res==1){
                                                                    alert('验证码已发送,请注意查收')
                                                                    $('#get_mobile_code').css('color','#ccc').text('重新获取验证码60').attr('rel','60');
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
                                            $('#get_mobile_code').text('重新获取验证码'+remainsecondes).attr('rel',remainsecondes);
                                            timing();
                                    },1000);
                            }else{
                                    $('#get_mobile_code').css('background-color','#00bbd3').text('获取验证码').attr('rel',0);
                            }
                        }
                        function ismobile(mobile) {
                            if (mobile.length == 0) {
                                    alert('请输入手机号码！');
                                    $('input [name=mobile]').focus();
                                    return false;
                            }
                            if (mobile.length != 11) {
                                    alert('请输入有效的手机号码！');
                                    $('input [name=mobile]').focus();
                                    return false;
                            }

                            var myreg = /^0?1[3|4|5|8][0-9]\d{8}$/;
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
				<div class="header"><a href="<?php echo site_url('login/index') ?>"><i class="iright">◇</i></a>学员注册</div>
			</header>
			<div class="mConts">
                            <p class="red"><?php echo $msg ?></p>
                            <form id="signupForm" action="" method="post" >
                                <input type="hidden" name="act" value="act" />
				<div class="iptBox">
                                    <input name="company_code" value="<?php echo $user['company_code'] ?>" type="text" class="ipt" placeholder="公司编号" />
					<p class="gray9  aLeft">公司编号，是您的公司编号，可以从您的培训经理处获得</p>
				</div>
				<div class="iptBox">
                                    <input id="mobile" name="mobile" value="<?php echo $user['mobile'] ?>" type="text" class="ipt" placeholder="手机号码" />
				</div>
				<div class="iptBox">
					<div class="iptInner">
                                            <input name="mobile_code" type="text" value="<?php echo $user['mobile_code'] ?>" class="ipt w60 fLeft" placeholder="验证码 " />
                                            <a id="get_mobile_code" href="javascript:void(0);" class="coBtn" rel="0">获取验证码</a>
					</div>
				</div>
				<div class="iptBox">
                                    <input name="user_pass" value="<?php echo $user['user_pass'] ?>" type="text" class="ipt" placeholder="设置密码" />
				</div>
                            <input type="submit" value="注册" class="blueBtnH40">
                            </form>
			</div>

		</article>

	</body>

</html>