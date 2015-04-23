<?php
/**
 * 常用函数库
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */
class tool
{


	/**
	 * url重写
	 * 例子：tool::url("index.php?c=index&a=cate&cid=11");
	 * 例子：tool::url("?c=index&a=cate&cid=11");
	 * 例子：tool::url(array('c'=>'index','a'=>'cate','cid'=>'11'));
	 */
	public static function url($avg)
	{
		//$url = "index.php?";
		if (is_array($avg))
		{
			foreach($avg as $k=>$v)
			{
				$url .= "&" . $k . "=" . $v;
			}
		}
		else
		{
			$url = $avg;
		}
		$url = str_replace("&&", "&", $url);
		$url = str_replace("?&", "?", $url);

		if (ENABLE_REWRITE || ENABLE_HTML)
		{
			//$url = "index.php?c=index&a=acc&action=edit";
			if(strpos($url , ".php") === false)
			{
				$url = "index.php" . $url;
			}
			$pos = strpos($url , ".php");
			//$newurl = substr($url, 0, $pos);
			$newurl = '';
			$lf = substr($url, $pos+4);
			$lf = str_replace("?", "&", $lf);
			if(strpos($lf , "&") !== false)
			{
				$avgs = split("\&", $lf);
				foreach ($avgs as $avg)
				{
					if($avg)
					{
						$av = split("=", $avg);
						if($av)
						{
							$newurl .= "-".	$av[0] . "_" . $av[1];
						}
					}
				}
			}
			//$newurl .= ".html";
			$newurl = substr($newurl, 1);
			//echo $newurl;
			$url = $newurl;
		}

		//tool::var_dump($url);
		return $url;
	}

	/**
	 * 计算页面的php解析时间 - 记录开始时间
	 * 
	 * 示例：
		 tool::evaltime_start();
		 echo "<br>该模块解析时间：" . tool::evaltime_end().'ms';
	 */
	public static function evaltime_start()
	{
		$_SESSION['time_start'] = tool::microtime_float();
	}	
	/**
	 * 计算页面的php解析时间 ms
	 */
	public static function evaltime_end()
	{
		$_SESSION['time_end'] = tool::microtime_float();
		$timediff = number_format(($_SESSION['time_end'] - $_SESSION['time_start']) * 1000, 2, '.', '') ;
		self::evaltime_start();
		//unset($_SESSION['time_end']);
		return $timediff;
	}	
	
	/**
	 * 获取系统时间-微秒
	 *
	 * @return float  
	 */
	public static function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	/**
	 * 获取系统时间-微秒 
	 *
	 * @return string  
	 */
	public static function microtime_string()
	{
		list($usec, $sec) = explode(" ", microtime());
		return (self::get_date($sec, "Y-m-d H:i:s") . " " . round($usec * 1000) . "ms");
	}

	/**
	 * 获取当前页面地址
	 *
	 * @return string
	 */
	public static function current_url()
	{
		if ($_SERVER["SERVER_PORT"]=='80')
			return 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
		else
			return 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}

}
?>