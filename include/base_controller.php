<?php
/**
 * 控制器基类
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */

class base_controller extends base
{
	//public $controller;
	//public $action;
	public $model;
	public $view;
	//public $validation;


	public function __construct()
	{
		parent::__construct();
		//$this->controller = glb::$controller;
		//$this->action = glb::$action;
		//$this->view = s('tpl');
		$this->view = new template();
		//$this->validation = s('validation', $this->controller, $this->action, $_SERVER['REQUEST_METHOD']);
	}
	
	public function __get($name)
	{
		if (isset($this->$name)) 
		{
			return $this->$name;
		} 
		else 
		{
			switch ($name)
			{
				case 'model':
					$this->model = s('m_'.$this->controller);
					break;
			}
			return $this->$name;
		}
	}
	
	
// 	public function assign($varname, $value)
// 	{
// 		$this->view->assign($varname, $value);
// 	}
	
// 	public function display($tplname)
// 	{
// 		$this->view->display($tplname);
// 	}

	/**
	 * 页面跳转
	 * @param string $message
	 * @param string Or array $url
	 * @param int $timeout	 默认:2秒跳转
	 * @param int $stop_loop   1:停止跳走,   默认0:自动跳转
	 */
	public function message($message, $urlData = null, $timeout = 2, $stop_loop=0)
	{
		$prefer = $_SERVER['HTTP_REFERER'];
		if ($timeout === 0)
		{
			header("Location:".tool::url($urlData?$urlData:$prefer));
			exit;
		}
		if (($pos = strpos($prefer, "?")) !== false)
		{
			$url_default = substr($prefer, $pos);
		}
		else
		{
			$url_default = $prefer;
		}

		if (is_array($urlData))
		{
			$urls = $urlData;
			$url_default = $urlData[0]['url'];
		}
		else
		{
			if ($urlData)
			{
				$urls[] = array(
					'txt' => $urlData,
					'url' => $urlData,
				);
				$url_default = $urlData;
			}
			else
			{
				$url_default = $_SERVER['HTTP_REFERER'];

			}
		}

		$this->view->assign('url_page', $url_default); //默认装向页面
		$this->view->assign('urlData', $urls);
		$this->view->assign('stop_loop', $stop_loop);
		$this->view->assign('message', $message);
		$this->view->assign('timeout', $timeout);
		//$this->view->display(PATH_ROOT . ADMIN . '/template/default/message.htm');
		$this->view->display( 'message');
		exit();
	}


	/**
	 * 搜索 自动下拉框 ajax
	 */
	public function ajax_search()
	{
		$q = strtolower($_GET["term"]);
		$fieldname = strtolower($_GET["fieldname"]);
		if (!$q) exit;
		$data = $this->model->get_list("$fieldname like '$q%'", 0, 12);

		$result = array();
		if(!empty($data["data"]))
		{
			foreach ($data["data"] as $value)
			{
				if (strpos(strtolower($value[$fieldname]), $q) !== false)
				{
					$product_id = $value["id"];
					$one = array(
						"id" => $product_id,
						"value" => strip_tags($value[$fieldname]),
					);
					array_push($result, $one);
				}
				if (count($result)>=10)
					break;
			}
		}
		echo json_encode($result);
		exit;
	}

	/**
	 * ajax 修改单个值
	 */
	public function ajax_set_value()
	{
		if ($_POST)
		{
			$id = (int)$_POST['id'];
			$val = $_POST['val']==1 ? 1 : 0;
			$setwhat = $_POST['setwhat'];

			$postdata = array(
				'id' => $id,
				$setwhat => $val,
			);
			$this->model->edit($postdata);
		}
	}
}

?>