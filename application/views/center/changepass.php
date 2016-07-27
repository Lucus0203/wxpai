<script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
            $( "#form" ).validate( {
                    rules: {
                            old_pass: {
                                    required: true
                            },
                            new_pass: {
                                    required: true,
                                    minlength: 5
                            },
                            confirm_pass: {
                                    required: true,
                                    equalTo: "#new_pass"
                            }
                    },
                    messages: {
                            old_pass: {
                                    required: "请输入原密码"
                            },
                            new_pass: {
                                    required: "请输入新密码",
                                    minlength: "密码长度不能小于 5 个字符"
                            },
                            confirm_pass: {
                                    required: "请再输入一次新密码",
                                    equalTo: "两次密码输入不一致"
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
<!--head-->
<header class="clearfix" id="gHeader">
        <div class="header">我的资料<a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts">
    <form id="form" method="post" action="">
        <input name="act" type="hidden" value="act" />
        <p class="red"><?php echo $msg ?></p>
        <dl class="dataDl">
                <dt>登录账号</dt>
                <dd>
                        <div class="noboriptBox">
                                <input type="text" class="noboript" readonly value="<?php echo $loginInfo['user_name'] ?>">
                        </div>
                </dd>
                <dt>原密码</dt>
                <dd>
                        <div class="noboriptBox">
                            <input name="old_pass" type='password' class="noboript" value="">
                        </div>
                </dd>
                <dt>新密码</dt>
                <dd>
                        <div class="noboriptBox">
                            <input id="new_pass" name="new_pass" type="password" class="noboript" value="">
                        </div>
                </dd>
                <dt>新密码确认</dt>
                <dd>
                        <div class="noboriptBox">
                            <input id="confirm_pass" name="confirm_pass" type="password" class="noboript" value="">
                        </div>
                </dd>

        </dl>
    <p><input type="submit" class=" blueBtnH40" value="保存 " /></p>
    </form>

</div>