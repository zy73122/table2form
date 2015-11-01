<?php
/**
 * 遍历MySQL表字段并按模板输出code
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/26 0006
 * Time: 16:30
 */

class c_ergodic extends base_controller {

    private $tableName;
    private $created_file = array();

    public function __construct()
    {
        $this->model = new m_createmodel();
        $this->view = new template();
    }

    public function index()
    {
        $this->show();
    }

    /*************** 显示创建首页 **************/
    public function show()
    {
        $dbconn = $_REQUEST['dbconn'] ? $_REQUEST['dbconn'] : ''; //连接哪个数据库

        //获取连接下面的所有表
        $this->model->choose_dbconn($dbconn);
        $tables = $this->model->getTables();
        $this->view->assign('tables', $tables);
        $this->view->assign('models', $this->model->getModels());

        //获取所有的数据库连接名称
        $dbconfs = glb::$config['database'];
        $this->view->assign('dbconns', array_keys($dbconfs));

        //获取可用的元素模板
        $elptpls = cfile::ls(PATH_TPLS.'element/');
        $this->view->assign('elptpls', $elptpls);

        if ($_POST) {
            $this->submit();
        }

        $this->view->display('ergodic/index');
    }

    public function submit()
    {
        try
        {
            $tablename = $_POST['tables'];
            $dbconn = $_POST['dbconn'];
            $model_source_en = $_POST['model_source_en']; //用来替换
            $element_tpl = $_POST['element_tpl']; //模板路径
            $model_source_replace = explode("\n", $_POST['model_source_replace']); //用来替换
            $modname_en = $_POST['modname']; //英文名

            $this->view->assign('modname_en', str_replace(' ','',ucwords(str_replace('_',' ',$modname_en))));

            if (!$tablename)
                throw new Exception("请选择表格");
            if (!$modname_en)
                throw new Exception("请输入新建的模型名称");
            if (!$dbconn)
                throw new Exception("请选择数据库连接");

            $this->model->choose_dbconn($dbconn);
            $modname_cn = $this->model->getTableComment($modname_en); //中文名

            foreach ($model_source_replace as $k=>$one)
            {
                $tmp = trim($one);
                if (!$tmp) unset($model_source_replace[$k]);
                if (strpos($one, "-")===false) unset($model_source_replace[$k]);
                $model_source_replace[$k] = trim(str_replace("\n", "", $one));
            }

            //生成字段html代码
            {
                $columns = $this->model->getColumns($tablename);
                $i=0;

                $element_path = 'element/'.$element_tpl.'/'; //模板路径

                //edit代码
                $autoColumnEditStr = ''; //编辑页面 字段列表
                $columnsTpl = array();
                //print_r($columns);exit;
                foreach ($columns as $k=>$onecolumn)
                {
                    $column_en = $onecolumn['column_name'];
                    $column_cn = $onecolumn['column_comment'] ? $onecolumn['column_comment'] : $column_en;
                    $column_type = $onecolumn['column_type'];
                    $column_key = $onecolumn['column_key'];
                    $column_extra = $onecolumn['extra'];
                    $column_default = $onecolumn['column_default'];
                    $column_is_nullable = $onecolumn['is_nullable'];

                    //该字段不需要生成
                    if ($column_key == 'PRI' || $column_extra == 'auto_increment' || $column_en=='updated' || $column_en=='created')
                    {
                        continue;
                    }

                    //显示元素类型。radio、select ……方式显示
                    $showElemType = '';
                    $vtype = '';
                    $vlen = '';
                    $vin = array();
                    if (in_array($column_en, array('is_effect','is_delete')))
                    {
                        $vin = array(0,1);
                        $showElemType = 'radio';
                    }
                    else if (preg_match('/(enum)(.*)/i', $column_type))
                    {
                        if (preg_match_all('/\'(.*)\'/iU', $column_type, $match))
                        {
                            $vin = $match[1];
                            if (count($vin)<=2) //radio
                            {
                                $showElemType = 'radio';
                            }
                            else //select
                            {
                                $showElemType = 'select';
                            }
                        }
                    }
                    else if (preg_match('/(mediumtext)(.*)/i', $column_type)) //textarea方式显示
                    {
                        $vtype = 'string';
                        $showElemType = 'textarea';
                    }
                    else if (preg_match('/(text|longtext)(.*)/i', $column_type)) //textarea方式显示
                    {
                        $vtype = 'string';
                        $showElemType = 'textarea_editor';
                    }
                    else if ($column_en=='updated' || $column_en=='created' || strpos($column_en, 'time') !== false || strpos($column_cn, '时间戳') !== false)
                    {
                        $vtype = 'int';
                        $showElemType = 'datetime';
                    }
                    else //input方式显示
                    {
                        if (preg_match('/(varchar|char)\((.*)\)/i', $column_type, $match)) {
                            $vtype = 'string';
                            $vlen = $match[2];
                        } else if (preg_match('/(int|tinyint|smallint|mediumint|bigint|integer)\((.*)\)/i', $column_type, $match)) {
                            $vtype = 'int';
                            //$vlen = $match[2];
                        } else if (preg_match('/(int|tinyint|smallint|mediumint|bigint|integer)/i', $column_type, $match)) {
                            $vtype = 'int';
                            //$vlen = $match[2];
                        } else if (preg_match('/(double|float)\((.*)\)/i', $column_type, $match)) {
                            $vtype = 'double';
                            //$vlen = $match[2];
                        } else if (preg_match('/(double|float)/i', $column_type, $match)) {
                            $vtype = 'double';
                            //$vlen = $match[2];
                        }
                        $showElemType = 'input';
                    }

                    $columnsTpl[] = array(
                        'column_en' => $column_en,
                        'column_cn' => $column_cn,
                        'column_type' => $column_type,
                        'column_key' => $column_key,
                        'column_extra' => $column_extra,
                        'column_default' => $column_default,
                        'column_is_nullable' => $column_is_nullable,
                        'column_elemtype' => $showElemType, //显示元素类型。radio、select ……方式显示
                        'column_vin' => $vin, //默认值列表。 为radio,select类型时，才有这个
                        'column_vtype' => $vtype, //值类型
                        'column_vlen' => $vlen, //长度限制
                    );

                    $i++;
                }
                //print_r($columnsTpl);exit;
                $this->view->assign('columns', $columnsTpl);

                //file_put_contents('tmp.php', $autoColumnEditStr);

                ob_start();
                $this->view->display($element_path.'top');
                $this->view->display($element_path.'center');
                $this->view->display($element_path.'bottom');
                $content = ob_get_clean();

//                //注意：输出内容含模板语法信息的需要替换回去
//                $content = str_replace('#', '$', $content);
//                $content = str_replace('[', '{', $content);
//                $content = str_replace(']', '}', $content);
                $content = str_replace('\\', '', $content);
            }


            //$this->message("生成代码.<br><textarea rows='20' cols='100'>".$content."</textarea>", null, 1, 1);
            $this->view->assign('content', $content);

        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
            //$this->message("出错了.<br>".$e->getMessage()."<br>", null, 100);
            $this->show_createmodel();
        }
    }


}