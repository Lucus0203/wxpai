<script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
            $( "#infoForm" ).validate( {
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
        <div class="header"><a href="<?php echo $homeUrl ?>"><i class="iright">◇</i></a>我的资料<a href="javascript:void(0);"><i class="ilevel">=</i></a></div>
        <?php $this->load->view ( 'rightbar' ); ?>
</header>
<div class="mConts">
    <p class="red"><?php echo $msg ?></p>
    <form id="infoForm" method="post" action="">
        <input type="hidden" name="act" value="act" />
        <dl class="dataDl">
                <dt>您的姓名</dt>
                <dd>
                    <div class="noboriptBox"><input name="name" type="text" class="noboript" value="<?php echo $loginInfo['name'] ?>" >
                        </div>
                </dd>
                <dt>您的工号</dt>
                <dd>
                    <div class="noboriptBox"><input name="job_code" type="text" class="noboript" value="<?php echo $loginInfo['job_code'] ?>">
                        </div>
                </dd>
                <dt>职业名称</dt>
                <dd>
                    <div class="noboriptBox"><input name="job_name" type="text" class="noboript" value="<?php echo $loginInfo['job_name'] ?>">
                        </div>
                </dd>
                <dt>所在部门</dt>
                <dd>
                    <div class="noboriptBox">
                        <select name="department_id" class="noboript">
                            <option value="">请选择</option>
                            <?php foreach ($departments as $d){ ?>
                            <option <?php if($loginInfo['department_id']==$d['id']){ ?>selected=""<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['level']>0?'&nbsp&nbsp':'' ?><?php echo $d['name'] ?></option>
                            <?php } ?>
                        </select>
                        </div>

                </dd>
                <dt>手机号码</dt>
                <dd>
                    <div class="noboriptBox"><input name="mobile" class="noboript" type="text" value="<?php echo $loginInfo['mobile'] ?>">
                        </div>
                </dd>
                <dt>电子邮箱</dt>
                <dd>
                    <div class="noboriptBox"><input name="email" class="noboript" type="text" value="<?php echo $loginInfo['email'] ?>">
                        </div>
                </dd>

        </dl>
    <p><input type="submit" class=" blueBtnH40" value="保存 " /></p>
    </form>
</div>