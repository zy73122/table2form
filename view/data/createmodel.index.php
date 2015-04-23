<?php include $this->gettpl('header'); ?>

<body id="main_page">

<div id="nav">
	<a href="?c=createmodel&a=index">创建模型</a>
</div>
<div class="wrap">
	<div class="container">
		<div id="main">
			<div class="con box-green">
				<form action="?c=createmodel&a=createmodel_submit" method="post" enctype="multipart/form-data">
					<div class="box-header">
						<h4>创建模型</h4>
					</div>
					<div class="box-content">
						<table class="table-font">
							<tr>
								<th class="w120">请选择连接：</th>
								<td><select name="dbconn" id="dbconn">
								<option value="">--请选择--</option>
								<?php foreach((array)$dbconns as $one) {?>
								<option value="<?php echo $one?>" <?php if($_REQUEST['dbconn'] == $one) { ?> selected<?php } ?>><?php echo $one?></option>
								<?php } ?>
								</select></td>
							</tr>
							<tr>
								<th class="w120">请选择表格：</th>
								<td><select name="tables" id="tables">
								<option value="">--请选择--</option>
								<?php foreach((array)$tables as $one) {?>
								<option value="<?php echo $one['table_name']?>"><?php echo $one['table_name']?> <?php if($one['isnew']=='yes') { ?>【新表】<?php } ?></option>
								<?php } ?>
								</select></td>
							</tr>
							<tr>
								<th class="w120">名称：</th>
								<td><input name="modname" type="text" class="textinput w270" id="modname" value="" /></td>
							</tr>
							<tr>
								<th class="w120">覆盖已有文件：</th>
								<td>
								<input type="radio" name="overwrite" id="overwrite_0" value="yes">
								覆盖
								<input type="radio" name="overwrite" id="overwrite_1" value="no" checked>
								不覆盖 <span class="red">(注意该选项，以免错误覆盖文件)</span></td>
							</tr>
							<tr>
								<th class="w120">生成表单元素：</th>
								<td>
								<input name="crcolumn" type="radio" id="crcolumn_0" value="yes" checked>
								生成
								<input type="radio" name="crcolumn" id="crcolumn_1" value="no">
								不生成 <span class="green">(智能生成提交表单中的字段)</span></td>
							</tr>
							<tr>
								<th class="w120">建模方式：</th>
								<td>
								<input type="radio" name="crtype" id="cr_new_from_sample" value="cp_sample" checked>
								复制sample模型
								<input type="radio" name="crtype" id="cr_copy" value="cp_other">
								复制已有的模型</td>
							</tr>
							<tr class="box_copy">
								<th class="w120">已有的模型：</th>
								<td><select name="model_source_en" id="model_source_en">
								<option value="">--请选择--</option>
								<?php foreach((array)$models as $one) {?>
								<option value="<?php echo $one?>"><?php echo $one?></option>
								<?php } ?>
								</select></td>
							</tr>
							<tr class="box_copy">
								<th class="w120">字符替换：</th>
								<td><textarea name="model_source_replace" cols="45" rows="5" class="textinput w270" id="model_source_replace">这是一个示例：
product-mymod
商品管理-我的模型
商品-我的模型</textarea>									<span class="green">(用来替换源码)</span></td>
							</tr>
						</table>
				  </div>
					<div class="box-footer">
						<div class="box-footer-inner">
							<input type="submit" class="formbtn" value="提交" />
							<input type="button" class="formbtn" value="返回" onClick="javascript:history.go(-1)" /> 
							<?php if($_REQUEST['id']) { ?>
							<input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']?>">
							<?php } ?>
							</div>
					</div>
				</form>
			</div>
			<!--/ con--> 

		</div>
	</div>
	<!--/ container--> 

</div>
<!--/ wrap--> 

<script type="text/javascript">
$(function(){
	//选择表事件
	$('#tables').change(function(){
		var tablename;
		tablename = $(this).val().replace(/jd_(.*)/g, "$1");
		$('#modname').val(tablename);
	});

	//选择建模方式事件
	$('.box_copy').hide();
	$('#cr_new_from_sample').click(function(){
		$('.box_copy').hide();
		$('.box_new').show();
	});
	$('#cr_copy').click(function(){
		$('.box_new').hide();
		$('.box_copy').show();
	});

	//
	$('#model_source_en').change(function(){
		var s = $(this).val();
		var sshort = s.substring(s.indexOf('_')+1);
		var modname = $('#modname').val();
		$('#model_source_replace').val(s+'-'+modname+'\n'+sshort+'-'+modname);
	});

	//
	$('#model_source_replace').focus(function(){
		if ($(this).val().indexOf('示例')!=-1)
		$(this).val('');
	});

	$('#dbconn').change(function(){
		self.location.href = '?c=createmodel&dbconn='+$(this).val();
	});


});



</script>

<?php include $this->gettpl('footer'); ?>