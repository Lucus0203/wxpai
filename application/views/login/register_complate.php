<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<title></title>
		<meta name="keywords" content="" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<meta name="Copyright" content="www.trainingpie.com" />
		<link rel="stylesheet" href="<?php echo base_url();?>css/common.css">
                <script type="text/javascript" src="<?php echo base_url();?>js/jquery1.83.js"></script>
		<script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				$( "#signupForm" ).validate( {
                                        rules: {
						name: {
							required: true
						},
						department_id: {
							required: true
						},
						email: {
							required: true,
							email: true
						}
					},
					messages: {
						
						name: {
							required: "请输入您的姓名",
							
						},
						department_id: {
							required: "请选择您的所在部门"
						},
						email: {
							required: "请输入您的电子邮箱",
							email: "请输入正确的电子邮箱"
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
			});
		</script>

	</head>

	<body>

		<article id="container">
			<!--head-->
			<header class="clearfix" id="gHeader">
				<div class="header"><a href="javascript:history.back();"><i class="iright">◇</i></a>完善基本资料</div>
			</header>
			<div class="mConts">
                            <form id="signupForm" action="" method="post" >
                                <input type="hidden" name="act" value="act" />
                                <div class="iptBox">
                                    <input name="name" value="<?php echo $user['name'] ?>" type="text" class="ipt" placeholder="您的姓名" />
                                </div>
                                <div class="iptBox">
                                    <label><input name="sex" value="1" type="radio" checked />男</label>
                                    <label><input name="sex" value="2" type="radio" />女</label>
                                </div>
                                <div class="iptBox">
                                    <input name="job_code" value="<?php echo $user['job_code'] ?>" type="text" class="ipt" placeholder="您的工号" />
                                </div>
                                <div class="iptBox">
                                    <input name="job_name" value="<?php echo $user['job_name'] ?>" type="text" class="ipt" placeholder="职位名称" />
                                </div>
                                <div class="iptBox">
                                    <select class="ipt" name="department_id" >
                                        <option value="">选择部门</option>
                                        <?php foreach($deaprtments as $d){?>
                                            <option value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="iptBox">
                                    <input name="email" value="<?php echo $user['email'] ?>" type="text" class="ipt" placeholder="电子邮件" />
                                </div>

                                <input type="submit" value="保存" class="blueBtnH40">
                            </form>
			</div>

		</article>

	</body>

</html>