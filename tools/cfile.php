<?php
/**
 * 文件读写
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */

class cfile
{
	/**
	 * 写文件
	 *
	 * @param string $filename
	 * @param string $data
	 * @param string $method
	 * @param int $iflock
	 * @param int $check
	 * @param int $chmod
	 * @return boolean
	 */
	public static function write($filename, $data, $method = 'wb+', $iflock = 1, $check = 1, $chmod = 1)
	{
		if (empty($filename))
		{
			return false;
		}

		if ($check && strpos($filename, '..') !== false)
		{
			return false;
		}

		if (!is_dir(dirname($filename)) && !self::mkdir_recursive(dirname($filename), 0700))
		{
			return false;
		}

		if (false == ($handle = fopen($filename, $method)))
		{
			return false;
		}

		if($iflock)
		{
			flock($handle, LOCK_EX);
		}
		fwrite($handle, $data);
		touch($filename);

		if($method == "wb+")
		{
			ftruncate($handle, strlen($data));
		}
		fclose($handle);
		$chmod && @chmod($filename,0777);

		return true;
	}

	/**
	 * 读文件
	 *
	 * @param string $filename
	 * @param string $method
	 * @return string
	 */
	public static function read( $filename, $method = "rb" )
	{
		if (strpos( $filename, '..' ) !== false)
		{
			return false;
		}
		if( $handle = @fopen( $filename, $method ) )
		{
			flock( $handle, LOCK_SH );
			$filedata = @fread( $handle, filesize( $filename ) );
			fclose( $handle );
			return $filedata;
		}
		else
		{
			return false;
		}
	}


	/**
	 * 删除文件
	 *
	 * @param string $filename
	 * @return boolean
	 */
	public static function rm($filename)
	{
		if (strpos($filename, '..') !== false)
		{
			return false;
		}

		return @unlink($filename);
	}


	/**
	 * 用递归方式创建目录
	 *
	 * @param string $pathname
	 * @param $mode
	 * @return boolean
	 */
	public static function mkdir_recursive($pathname, $mode = 0700)
	{
		$pathname .= 'xx.log';
		//byy1.08
		if (DEBUG_LEVEL)
		{
			error_log("mkdir_recursive, $pathname, $mode\n", 3, 'debug.log');
		}
		$pathname = rtrim(preg_replace(array('/\\{1,}/', '/\/{2,}/'), '/', $pathname), '/');
		$dotpos = strrpos($pathname, '/');
		//如果是路径中带文件名
		if ($dotpos !== false && strpos(substr($pathname, $dotpos), '.') !== false) {
			$pathname = dirname($pathname);
		}
		if (is_dir($pathname))
		{
			return true;
		}
		return mkdir($pathname, $mode, $recursive = 1);
	}


	/**
	 * 用递归方式删除目录
	 *
	 * @param string $file
	 * @return boolean
	 */
	public static function rm_recurse($file)
	{
		if (strpos( $file, '..' ) !== false && strpos( $file, '/../' )===false )
		{
			return false;
		}

		if (is_dir($file) && !is_link($file))
		{
			foreach(scandir($file) as $sf)
			{
				if($sf === '..' || $sf === '.')
				{
					continue;
				}
				if (!self::rm_recurse($file . '/' . $sf))
				{
					return false;
				}
			}
			return @rmdir($file);
		}
		else
		{
			return unlink($file);
		}
	}


	/**
	 * 引用文件安全检查
	 *
	 * @param string $filename
	 * @param int $ifcheck
	 * @return boolean
	 */
	public static function check_security($filename, $ifcheck=1)
	{
		if (strpos($filename, 'http://') !== false) return false;
		if (strpos($filename, 'https://') !== false) return false;
		if (strpos($filename, 'ftp://') !== false) return false;
		if (strpos($filename, 'ftps://') !== false) return false;
		if (strpos($filename, 'php://') !== false) return false;
		if (strpos($filename, '..') !== false) return false;

		return $filename;
	}

	/**
	 * 文件列表
	 *
	 * @param string $path //路径
	 * @param string $type[optional] //类型:file 文件，dir 目录, 缺省 file+dir
	 * @return array
	 */
	public static function ls($path, $type = '')
	{
		if(!is_dir($path))
		{
			return false;
		}
		if(!empty($type) && in_array($type, array('file', 'dir')))
		{
			$func = "is_" . $type;
		}
		$files = scandir($path);
		foreach($files as $k => $cur_file)
		{
			if ($cur_file=="." || $cur_file==".." || $cur_file == '.svn' || ($func && !$func($path . '/' . $cur_file)))
			{
				unset($files[$k]);
			}
		}

		return $files;
	}

	/**
	 * 计算目录大小
	 */
	public static function dirsize($dir)
	{
		$dh = opendir($dir);
		$size = 0;
		while($file = readdir($dh))
		{
			if($file != '.' and $file != '..')
			{
				$path = $dir."/".$file;
				if (is_dir($path))
				{
					$size += dirsize($path);
				}
				else
				{
					$size += filesize($path);
				}
			}
		}
		closedir($dh);
		return $size;
	}

	/**
	 * 获取文件大小（包含远程文件）
	 */
	public static function get_file_size($url)
	{
		$url = parse_url($url);
		if ($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error))
		{
			fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
			fputs($fp,"Host:$url[host]\r\n\r\n");
			while(!feof($fp))
			{
				$tmp = fgets($fp);
				if(trim($tmp) == '')
				{
					break;
				}
				else if(preg_match('/Content-Length:(.*)/si',$tmp,$arr))
				{
					return trim($arr[1]);
				}
			}
			return null;
		}
		else
		{
			return null;
		}
	}
}
