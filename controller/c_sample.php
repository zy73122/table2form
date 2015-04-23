<?php
/**
 * 示例管理 该控制器由系统生成
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */
class c_sample extends base_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new m_sample();
    }

    public function index()
    {
        $this->list_index();
    }

    /**
     * 列表显示
     */
    public function list_index()
    {
        try
        {
            $start = $_GET['start'] ? (int)$_GET['start'] : 0;
            $conditions = "1";
            $urls = tool::current_url();

            //<!--列表头部ctl数据--><!--903-->
            if ($username = $_GET['username']) //用户名搜索
            {
                $conditions .= " AND username like '%$username%'";
                $urls .= "&username=$username";
            }
            if ($nickname = $_GET['nickname']) //昵称搜索
            {
                $conditions .= " AND nickname like '%$nickname%'";
                $urls .= "&nickname=$nickname";
            }
            $isguest = isset($_GET["isguest"]) ? $_GET["isguest"] : "";
            if ($isguest !== "") //帐号状态，=0为禁用，=1为正常，默认为=1列表检索
            {
                $conditions .= " AND isguest = '$isguest'";
                $urls .= "&isguest=$isguest";
            }
            //<!--903--><!--列表头部ctl数据 end-->

            $list = $this->model->get_list($conditions, $start, PAGE_ROWS);
            $this->view->assign('page_url', tool::current_url());

            $this->view->assign('data', $list['data']); //列表数据
            $this->view->assign('pages', pager::get_page_number_list($list['total'], $start, PAGE_ROWS));
        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
        }

        $this->view->display('sample/list');
    }

    /**
     * 添加编辑
     */
    public function edit()
    {
        try
        {
            if($_SERVER["REQUEST_METHOD"] == "POST")
            {
                if ($id=(int)$_POST['id']) //编辑
                {
                    $postdata = $this->_checkdata();
                    $this->model->edit($postdata);
                    $urllist = array(
                        array(
                            'txt' => "返回编辑页面",
                            'url' => U("sample/edit?id=$id"),
                        ),
                        array(
                            'txt' => "返回列表页面",
                            'url' => U("sample"),
                        ),
                    );

                    $this->message("编辑数据成功!", $urllist);
                }
                else //添加
                {
                    $postdata = $this->_checkdata();
                    $this->model->add($postdata);
                    $urllist = array(
                        array(
                            'txt' => "继续添加",
                            'url' => U("sample/edit?parent=".$_POST['parent']),
                        ),
                        array(
                            'txt' => "返回列表页面",
                            'url' => U("sample"),
                        ),
                    );
                    $this->message("添加数据成功!", $urllist);
                }
            }
            else
            {
                $id = (int)$_GET['id'];
                $data = $id ? $this->model->get_row($id) : array();
            }
        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
        }

        $this->view->assign('data', $data);
        $this->view->display('sample/edit');
    }

    /**
     * 检测数据
     *
     * @return array
     */
    protected function _checkdata()
    {
        $postdata = $_POST;
        $postdata['id'] = $_REQUEST['id'];

        return $postdata;
    }

    /**
     * 列表页面的form提交
     */
    public function list_submit()
    {
        try
        {
            if ($_POST['action'] == "order")
            {
                $this->list_order();
            }
            else if ($_POST['action'] == "delete")
            {
                $this->list_delete();
            }
            else
            {
                throw new Exception('没有要执行的动作');
            }
        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
        }
        $this->list_index();
    }

    /**
     * 删除 列表页面
     */
    protected function list_delete()
    {
        try
        {
            $data = is_array($_POST['id']) ? $_POST['id']:array();
            $id_list = $_POST['id'];
            if (is_array($id_list) && !empty($id_list))
            {
                $id_list = implode(',', $id_list);
            }
            $this->model->delete($data);
            $this->message("删除数据成功.", U("sample"));
        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
        }
    }

    /**
     * 设置排序 列表页面
     */
    protected function list_order()
    {
        try
        {
            $data = is_array($_POST['orderby']) ? $_POST['orderby']:array();
            $this->model->order($data);
            $this->message("排序设置成功.");
        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
        }
    }

    /**
     * pre钩子方法
     */
    public function pre()
    {
    }

    /**
     * post钩子方法
     */
    public function post()
    {
    }

}
?>