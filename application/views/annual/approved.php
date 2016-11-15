<header class="clearfix mb0" id="gHeader">
    <div class="header">年度审核</div>
</header>
<div class="mConts p0">

    <?php if(count($students)>0){ ?>
    <table class="tableA">
        <col width="2.5%" />
        <col width="23%" />
        <col width="23%" />
        <col width="23%" />
        <col width="23%" />
        <col width="2.5%" />
        <tr>
            <th></th>
            <th>姓名</th>
            <th>工号</th>
            <th>课程数</th>
            <th>操作</th>
            <th></th>
        </tr>
        <?php foreach ($students as $s){ ?>
        <tr>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>></td>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>><?php echo $s['name'] ?></td>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>><?php echo $s['job_code'] ?></td>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>><?php echo round($s['num']).'/'.round($s['total']) ?></td>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>><a class="blue" href="<?php echo site_url('annualmanage/approvedlist/'.$plan['id'].'/'.$s['id']) ?>">审核</a></td>
            <td <?php if($s===end($students)){?>class="noborder"<?php } ?>></td>
        </tr>
        <?php } ?>
        <tr>
            <td style="border-top:7px solid #e6e6e6;"></td>
            <td style="border-top:7px solid #e6e6e6;"></td>
            <td style="border-top:7px solid #e6e6e6;"></td>
            <td style="border-top:7px solid #e6e6e6;">部门预算:</td>
            <td style="border-top:7px solid #e6e6e6;">¥<?php echo round($budget) ?></td>
            <td style="border-top:7px solid #e6e6e6;"></td>
        </tr>
    </table>
    <?php }else{ ?>
        <p class="tipsF14">暂时没有需要审核内容。</p>
    <?php } ?>



</div>