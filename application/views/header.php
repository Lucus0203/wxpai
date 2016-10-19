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
        <link rel="stylesheet" href="<?php echo base_url() ?>css/font-awesome.min.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>css/common.css?0819">
		<script type="text/javascript" src="<?php echo base_url() ?>js/jquery1.83.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				var chk=1;
			 	$('.rightBarBox').click(function(event){
			 		event.stopPropagation();
			 	});
			 	
				$('.ilevel').click(function(event) {
					event.stopPropagation();
					if(chk==1){
						$('.rightBarBox').slideDown();
						$('.rightBar').css('left','50%');
						chk=0;
					}else{
						$('.rightBarBox').slideUp();
						$('.rightBar').css('left','100%');
						chk=1;
					}
					return false;
					
				});
                //隐藏侧边栏
                $('.mapBg').click(function(){
                        $('.rightBarBox').slideUp();
                        $('.rightBar').css('left','100%');
                        chk=1;
                });
			
			})
		</script>

	</head>

	<body>

		<article id="container">