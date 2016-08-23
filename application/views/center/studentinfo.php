<script type="text/javascript" src="<?php echo base_url();?>js/jquery.validate.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('select[name=department_parent_id]').change(function(){
            var departmentid=$(this).val();
            $.ajax({
                type:"post",
                url:'<?php echo site_url('center/ajaxDepartment') ?>',
                data:{'departmentid':departmentid},
                datatype:'jsonp',
                success:function(res){
                    var json_obj = $.parseJSON(res);
                    var count=0;
                    var str='<option value="'+departmentid+'">请选择</option>';
                    $.each(json_obj.departs,function(i,item){
                        str+='<option value="'+item.id+'">'+item.name+'</option>';
                        ++count;
                    });
                    if(count>0){
                        $('select[name=department_id]').show().html(str)
                    }else{
                        $('select[name=department_id]').hide().html('<option value="'+departmentid+'" selected >请选择</option>');
                    }
                }
            });
        });
        jQuery.validator.addMethod("isMobile", function(value, element) {
            var length = value.length;
            var mobile = /^((1[0-9]{2})+\d{8})$/;
            return this.optional(element) || (length == 11 && mobile.test(value));
        }, "请正确填写您的手机号码");
        $( "#infoForm" ).validate( {
            rules: {
                name: {
                    required: true
                },
                job_code: {
                    required: true
                },
                job_name: {
                    required: true
                },
                department_parent_id: {
                    required: true
                },
                mobile: {
                    required: true
                },
                email: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "请输入您的姓名"
                },
                job_code: {
                    required: "请输入您的工号"
                },
                job_name: {
                    required: "请输入您的职业名称"
                },
                department_parent_id: {
                    required: "请选择您所在的部门"
                },
                mobile: {
                    required: "请输入您的手机号码"
                },
                email: {
                    required: "请输入您的点子邮箱"
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
    <p class="red"><?php echo $msg ?></p>
    <form id="infoForm" method="post" action="">
        <input type="hidden" name="act" value="act" />
        <dl class="dataDl">
                <dt><label for="name">您的姓名</label></dt>
                <dd>
                    <div class="noboriptBox">
                        <input id="name" name="name" type="text" class="noboript" value="<?php echo $loginInfo['name'] ?>" >
                    </div>
                </dd>
                <dt><label for="job_code">您的工号</label></dt>
                <dd>
                    <div class="noboriptBox"><input id="job_code" name="job_code" type="text" class="noboript" value="<?php echo $loginInfo['job_code'] ?>">
                        </div>
                </dd>
                <dt><label for="job_name">职业名称</label></dt>
                <dd>
                    <div class="noboriptBox"><input id="job_name" name="job_name" type="text" class="noboript" value="<?php echo $loginInfo['job_name'] ?>">
                        </div>
                </dd>
                <dt><label for="department_parent_id">所在部门</label></dt>
                <dd>
                    <div class="noboriptBox">
                        <select id="department_parent_id" name="department_parent_id" class="noboript">
                            <option value="">请选择</option>
                            <?php foreach ($departments as $d){ ?>
                            <option <?php if($loginInfo['department_parent_id']==$d['id']){ ?>selected=""<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                            <?php } ?>
                        </select>
                        <select id="department_id" name="department_id" <?php if(count($second_departments)<=0){?>style="display: none;"<?php } ?> class="noboript">
                            <option value="">请选择</option>
                            <?php foreach ($second_departments as $d){ ?>
                                <option <?php if($loginInfo['department_id']==$d['id']){ ?>selected=""<?php } ?> value="<?php echo $d['id'] ?>"><?php echo $d['name'] ?></option>
                            <?php } ?>
                        </select>
                        </div>

                </dd>
                <dt><label for="mobile">手机号码</label></dt>
                <dd>
                    <div class="noboriptBox"><input id="mobile" name="mobile" class="noboript" type="text" value="<?php echo $loginInfo['mobile'] ?>">
                        </div>
                </dd>
                <dt><label for="email">电子邮箱</label></dt>
                <dd>
                    <div class="noboriptBox"><input id="email" name="email" class="noboript" type="text" value="<?php echo $loginInfo['email'] ?>">
                        </div>
                </dd>

        </dl>
    <p><input type="submit" class=" blueBtnH40" value="保存 " /></p>
    </form>
</div>