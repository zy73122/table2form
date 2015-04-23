<?php
/**
 * 创建模型
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */
class m_createmodel extends base_model
{
	/*************** 友情链接 后台 **************/
	public $db;
	public $dsn;

	public function __construct()
	{
	}
	
	public function choose_dbconn($dbconn = '')
	{
// 		if ($dbconn) {
// 			$dsn = glb::$config['database'][$dbconn][0];
// 		} else {
// 			$dsn = db::instance()->get_current_config();
// 		}
		if ($dbconn) {
			$this->db = dbmysqli::instance($dbconn);
		} else {
			$this->db = dbmysqli::instance();
		}
		$this->db->query('SET names utf8');
		$this->dsn = $this->db->get_current_config();
	}
	
	/**
	 * 获取所有的表
	 *
	 * @return array 表格名
	 */
	public function getTables()
	{
		$dbname = $this->dsn['database'];
		//if ($this->db->query("show tables"))
		if ($this->db->query("select table_name,create_time from information_schema.tables where table_schema='".$dbname."'"))
		{
			$data = $this->db->fetch_all();
			$i = 0;
			foreach ($data as $k=>$one)
			{
				//$table_name = array_pop($one);
				$table_name = $one['table_name'];
				$tables[$i]['table_name'] = $table_name;
				
				/*//已有了模型的话
				$shortname = str_replace(TABLE_PREFIX, '', $table_name);
				if (in_array($shortname, self::getModels()))
				$tables[$i]['hasModel'] = 'yes';*/
				
				//今天创建的表的话
				$create_time = strtotime($one['create_time']);
				if ($create_time && time()-$create_time<2*24*3600)
				$tables[$i]['isnew'] = 'yes';
				/*//存储过程方式
				if ($this->db->query("call get_table_create_time('$table_name');"))
				{
					$d = $this->db->fetch_row();
					$createtime = strtotime($d['create_time']);
					if ($createtime && time()-$createtime<24*3600)
					$tables[$i]['isnew'] = 'yes';
				}*/
				
				$i++;
			}
		}
		return empty($tables)? array() : $tables;
	}

	/**
	 * 获取表的字段信息
	 *
	 * @param string $table_name
	 * @return array
	 */
	public function getColumns($table_name)
	{
		$dbname = $this->dsn['database'];
		/*if ($this->db->query("show columns from $table_name"))
		{
		$data = $this->db->fetch_all();
		$newarr = array();
		foreach ($data as $k=>$onecolumn)
		{
		$newarr[$k]['Field'] = $onecolumn['Field']; //
		$newarr[$k]['Type'] = $onecolumn['Type']; //int(10) unsigned、varchar(60)、decimal(10,2)、enum('no','yes')
		$newarr[$k]['Null'] = $onecolumn['Null']; //NO、YES
		//$newarr[$k]['Key'] = $onecolumn['Key']; //PRI、""
		$newarr[$k]['Default'] = $onecolumn['Default']; // NULL、""、"0"
		//$newarr[$k]['Extra'] = $onecolumn['Extra']; //auto_increment、""

		if ($onecolumn['Key'] == 'PRI')
		{
		unset($newarr[$k]);
		continue;
		}
		if (preg_match('/(enum)(.*)/i', $onecolumn['Type']))
		{
		$newarr[$k]['Type'] = "radio";
		}
		else
		{
		$newarr[$k]['Type'] = "text";
		}
		}
		}*/
		//column_type  		int(10) unsigned、varchar(60)、decimal(10,2)、enum('no','yes')
		//column_key 		PRI、""
		//extra 			auto_increment、""
		//is_nullable		NO、YES
		//column_default	NULL、""、"0"
		if ($this->db->query("SELECT column_name,column_type,column_key,extra,column_comment,is_nullable,column_default  from information_schema.columns WHERE table_name ='$table_name' and table_schema='".$dbname."'"))
		{
			$data = $this->db->fetch_all();
		}

		return empty($data)? array() : $data;
	}

	/**
	 * 获取表的标注
	 *
	 * @param string $table_name
	 * @return string 
	 */
	public function getTableComment($table_name)
	{
		if ($this->db->query("show table status where name='#PRE#$table_name'"))
		{
			$one = $this->db->fetch_row();
			$comment = $one['Comment'];
		}
		return $comment ? $comment : null;
	}

	/**
	 * 获取已有的模型
	 *
	 * @return unknown
	 */
	public function getModels()
	{

		//过滤数组
		$filters = array('cache','config','log','login','plan','shop_call','update');
		
		$data_models = cfile::ls(PATH_MODEL, 'file');
		$data_controllers = cfile::ls(PATH_CONTROLLER, 'file');
		foreach ($data_controllers as $k=>$onectl)
		{
			$shortname = preg_replace("/c_(.*)\.php/i", "$1", $onectl);
			$modlename = 'm_'.$shortname.'.php';
			if (in_array($modlename, $data_models) && !in_array($shortname, $filters))
			$newarr[] = $shortname;
		}
		
		return $newarr;
	}


}

?>