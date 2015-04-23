
<!--模型数据-->
<!--该代码由系统自动生成-->
<table class="table-font">
    <tr>
        <th class="w120"><?php echo $column_cn?>：</th>
        <td>'.<?php echo $centerEditStr?>.'</td>
    </tr>
    <?php foreach((array)$columns as $onecolumn) {?>
    {<?php echo $column_en?> = <?php echo $onecolumn['column_name']?>;}
    {<?php echo $column_cn?> = <?php echo $onecolumn['column_comment']?> ? <?php echo $onecolumn['column_comment']?> : <?php echo $column_en?>;}
    {<?php echo $column_type?> = <?php echo $onecolumn['column_type']?>;}
    {<?php echo $column_key?> = <?php echo $onecolumn['column_key']?>;}
    {<?php echo $extra?> = <?php echo $onecolumn['extra']?>;}
    {<?php echo $column_default?> = <?php echo $onecolumn['column_default']?>;}
    <tr>
        <th style="VERTICAL-ALIGN: top"><?php echo $column_cn?>：</th>
        <td>'.<?php echo $centerEditStr?>.'</td>
    </tr>
    <?php } ?>
</table>
<!--该代码由系统自动生成 end-->
<!--模型数据 end-->