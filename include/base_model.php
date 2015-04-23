<?php
/**
 * 模型基类
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */

class base_model extends base
{
	public $db;
	public $table_name;
	public $table_prid;
	public function __construct()
	{
		parent::__construct();
		//$this->db = s('dbmysqli', glb::$config['database']['default']);
		$this->db = dbmysqli::instance();
		//$this->db = db::instance();
	}

	//======================== 常规处理 =====================================================================
	/**
	 * 获取列表
	 *
	 * @param string $condition
	 * @param int $start
	 * @param int $num
	 * @return array
	 */
	public function get_list($where="", $start=0, $num=0, $order="")
	{
		if (is_array($where))
		{
			$tmp = '';
			foreach ($where as $k => $v)
			{
				$tmp .= $tmp ? " and $k='$v'" : "$k='$v'";
			}
			$where = $tmp;
		}
		$sql = "select SQL_CALC_FOUND_ROWS *,".$this->table_prid." as id from #PRE#".$this->table_name ." ";
		$sql .= $where ? "where $where " : '';
		$sql .= $order ? "order by $order " : "order by ".$this->table_prid." desc ";
		$sql .= ($start>-1 && $num>0) ? "limit $start,$num" : '';
		$this->db->query($sql);
		$data = $this->db->fetch_all();
		$this->db->query('SELECT FOUND_ROWS();');
		$nums = $this->db->fetch_one();
		$output = array();
		$output['data'] = $data;
		$output['total'] = $nums;
		return $output;
	}
	
	/**
	 * 获取单条信息
	 *
	 * @param string $id
	 * @return array
	 */
	public function get_row($id)
	{
		if (!$id)
			throw new Exception("条件为空.");
	
		$data = $this->db->select($this->table_name, "*,".$this->table_prid." as id", $this->table_prid."=$id");
		return $data[0];
	}
	
	/**
	 * 添加
	 *
	 * @param array $data
	 */
	public function add($data = array())
	{
		if(!is_array($data))
			throw new Exception("要添加的数据为空.");
		unset($data['id']);
		$this->db->insert($this->table_name, $data);
	}
	
	/**
	 * 编辑
	 *
	 * @param array $data
	 */
	public function edit($data = array())
	{
		if(!is_array($data))
			throw new Exception("编辑的数据为空.");
		$id = (int)$data['id'];
		if(!$id)
			throw new Exception("id为空");
		$condition = $this->table_prid." = '$id'";
		unset($data['id']);
		$this->db->update($this->table_name, $data, $condition);
	}
	
	/**
	 * 编辑
	 * $data 是关于'id'的数据组
	 *
	 * @param array $data
	 */
	public function delete($data = array())
	{
		if (!is_array($data))
			throw new Exception("请选择删除数据.");
		$condition = implode("','", $data);
		$this->db->delete($this->table_name, $this->table_prid." in('$condition') ");
	}
	
	/**
	 * 排序保存
	 */
	public function order($data = array())
	{
		if(!is_array($data))
			throw new Exception("提交排序参数错误,请重试.");
		foreach($data as $id=>$orderby)
		{
			$id = (int)$id;
			$orderby = (int)$orderby;
			$updatedata = array(
					'displayorder' => $orderby,
			);
			$this->db->update($this->table_name, $updatedata, $this->table_prid." ='$id' ");
		}
	}
	//======================== 常规处理end =====================================================================
	
	//<-- 非常规
	//-->
	
	/**
	 * 获得最大id
	 */
	public function get_max_id()
	{
		$maxid = 0;
		$data = $this->db->select($this->table_name, " MAX(id)");
		if ($data)
		{
			$maxid = $data[0];
		}
		return $maxid;
	}


}

?>