<?php include $this->gettpl('header'); ?>

这是变量输出: <?php echo $status?> 
<br>

这是if\loop输出: 
<?php if($choose ) { ?> 
1<br>
<?php foreach((array)$applist as $key => $val) {?>
						<?php echo $key?> -- <?php echo $val?> <br>
<?php }?>
<?php } else { ?> 
2 <br>
<?php } ?>

这是变量设置:
<?php $i = 'setval';?>
i = <?php echo $i?>		
<br>

这是执行php代码:
<?php print_r($applist);var_dump(time());?>	
<br>

这是lang设置(template.class.php):
title = 标题
<br>




			
