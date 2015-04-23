<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>网址跳转中……</title>
<link href="<?php echo $url_tpl?>css/tool_message.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="success">
	<p>&nbsp;</p>
	<p><strong> <?php echo $message?>
		<?php if($stop_loop!=1) { ?> <span id="seconds" style="color:#f60;"><?php echo $timeout?></span>秒后自动返回
		<?php } ?> </strong> </p>
	<p>	<ul id="cont">
		<?php foreach((array)$urlData as $one) {?>
			<li> <a href="<?php echo $one['url']?>"><?php echo $one['txt']?></a> </li>
		<?php } ?>
		</ul>
	</p>
	<p> <a href="<?php echo $url_page?>">如果您的浏览器没有自动跳转，点击返回！ </a> </p>
	<p>&nbsp;</p>
</div>
<?php if($stop_loop!=1) { ?>
<script type="text/javascript">

var i = '<?php echo $timeout?>';
var reTime = setInterval(function(){
	i = i-1;
	if(i<0){

		window.location.href= '<?php echo $url_page?>'
		window.clearInterval(reTime);
		return;

	}
	document.getElementById("seconds").innerHTML = i;
},1000);

</script> 
<?php } ?>
</body>
</html>
