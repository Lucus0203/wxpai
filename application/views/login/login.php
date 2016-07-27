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
		<meta name="Copyright" content="www.training.com" />
		<link rel="stylesheet" href="<?php echo base_url();?>css/common.css">

	</head>

	<body>

		<article id="container">
			<!--head-->
			<header class="clearfix" id="gHeader">
				<div class="mLogo">
<!--					<a href=""><img alt="" src="--><?php //echo base_url();?><!--images/logo_login.png"></a>-->
					<h1 class="white"><?php echo !empty($company_name)?$company_name:'培训派' ?></h1>
				</div>
			</header>
			<div class="mConts">
                            <form method="post" action="">
                                <input type="hidden" name="act" value="act" />
				<div class="titp">学员用户登录</div>
                                <p class="red"><?php echo $error_msg ?></p>
				<div class="iptBox">
                                    <input name="mobile" type="mobile" class="ipt" placeholder="手机号码" /></div>
				<div class="iptBox">
                                    <input name="password" type="password" class="ipt" placeholder="登录密码" /></div>
				<input type="submit" value="登录" class="blueBtnH40">
                            </form>
                            <p class="f18 aCenter gray9 mb20">还不是注册学员？</p>
                            <a href="<?php echo site_url('login/register1') ?>" class="borBlueBtnH40">立即注册</a>
			</div>

		</article>
		<script>
			var _hmt = _hmt || [];
			(function() {
				var hm = document.createElement("script");
				hm.src = "//hm.baidu.com/hm.js?b84ccdce1e3ffd049fdf347fc743928b";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hm, s);
			})();
		</script>
	</body>

</html>