<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/6 0006
 * Time: 9:21
 */

class c_createmodel extends base_controller {

    private $tableName;
    private $created_file = array();

    public function __construct()
    {
        $this->model = new m_createmodel();
        $this->view = new template();
    }

    public function index()
    {
        $this->show_createmodel();
    }

    /*************** 显示创建首页 **************/
    public function show_createmodel()
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

        $sys = array('subform'=>'?m=createmodel&a=createmodel_submit');
        $this->view->assign('sys', $sys);
        $this->view->display($this->view->_dir . 'createmodel.index', null, "admin");
    }

    public function createmodel_submit()
    {
        try
        {
            $tablename = $_POST['tables'];
            $overwrite = $_POST['overwrite']=='yes' ? true : false; //是否覆盖已有文件
            $crcolumn = $_POST['crcolumn']=='yes' ? true : false; //智能生成提交表单中的字段
            $crtype = $_POST['crtype'] ? $_POST['crtype'] : 'cp_sample'; //新建模型 或 复制已有模型
            $dbconn = $_POST['dbconn'];
            $model_source_en = $_POST['model_source_en']; //用来替换
            $model_source_replace = explode("\n", $_POST['model_source_replace']); //用来替换
            $modname_en = $_POST['modname']; //英文名

            if (!$tablename)
                throw new Exception("请选择表格");
            if (!$modname_en)
                throw new Exception("请输入新建的模型名称");
            if (!$crtype && !$model_source_en)
                throw new Exception("请选择要复制的源模型");
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
            if ($crcolumn)
            {
                $columns = $this->model->getColumns($tablename);
                $i=0;
                //edit代码
                $autoColumnEditStr = ''; //编辑页面 字段列表
                foreach ($columns as $k=>$onecolumn)
                {
                    $column_en = $onecolumn['column_name'];
                    $column_cn = $onecolumn['column_comment'] ? $onecolumn['column_comment'] : $column_en;
                    $column_type = $onecolumn['column_type'];
                    $column_key = $onecolumn['column_key'];
                    $extra = $onecolumn['extra'];
                    $column_default = $onecolumn['column_default'];

                    //该字段不需要生成
                    if ($column_key == 'PRI' || $extra == 'auto_increment' || $column_en=='updated' || $column_en=='created')
                    {
                        continue;
                    }

                    $centerEditStr = '';
                    //radio、select方式显示
                    if (preg_match('/(enum)(.*)/i', $column_type))
                    {
                        if (preg_match_all('/\'(.*)\'/iU', $column_type, $match))
                        {
                            $valarr = $match[1];
                            if (count($valarr)<=2) //radio
                            {
                                foreach ($valarr as $oneval)
                                {
                                    $centerEditStr .= '<input type="radio" name="'.$column_en.'" id="'.$column_en.'" value="'.$oneval.'" {if $data["'.$column_en.'"] !== null }{if $data["'.$column_en.'"]=="'.$oneval.'"}checked{/if}{else}';
                                    if ($column_default == $oneval)
                                        $centerEditStr .= 'checked';
                                    $centerEditStr .= '{/if}>'.$oneval.' ';
                                }
                            }
                            else //select
                            {
                                $centerEditStr = '<select name="'.$column_en.'" id="'.$column_en.'">';
                                $centerEditStr .= '<option value="">--请选择--</option>';
                                foreach ($valarr as $oneval)
                                {
                                    $centerEditStr .= '<option value="'.$oneval.'" {if $data["'.$column_en.'"] !== null }{if $data["'.$column_en.'"]=="'.$oneval.'"}selected="selected"{/if}{else}';
                                    if ($column_default == $oneval)
                                        $centerEditStr .= 'selected="selected"';
                                    $centerEditStr .= '{/if}>'.$oneval.'</option>';
                                }
                                $centerEditStr .= '</select>';
                            }
                        }
                    }
                    else if (preg_match('/(mediumtext)(.*)/i', $column_type)) //textarea方式显示
                    {
                        $centerEditStr = '<textarea name="'.$column_en.'" class="textinput w270" id="'.$column_en.'" cols="45" rows="5">{$data["'.$column_en.'"]}</textarea>';
                    }
                    else if (preg_match('/(text|longtext)(.*)/i', $column_type)) //textarea方式显示
                    {
                        //在线文本编辑器
                        $editor = new editor('Mine', 'white', 'zh-cn');
                        $editor->setSimple();
                        $centerEditStr = $editor->createHtml($column_en, '{$data['.$column_en.']}');
                    }
                    else if ($column_en=='updated' || $column_en=='created' || strpos($column_en, 'time') !== false || strpos($column_cn, '时间戳') !== false)
                    {
                        $centerEditStr = '<input name="'.$column_en.'" class="textinput w270" id="'.$column_en.'" value="{if $data["'.$column_en.'"]}{eval echo date("Y-m-d H:i:s", $data["'.$column_en.'"])}{/if}" />';
                    }
                    else //input方式显示
                    {
                        $centerEditStr = '<input name="'.$column_en.'" class="textinput w270" id="'.$column_en.'" value="{$data["'.$column_en.'"]}" />';
                    }

                    if ($i == 0)
                        $autoColumnEditStr .= <<<EOF
                            <tr>
                                <th class="w120">{$column_cn}：</th>
                                <td>$centerEditStr</td>
                            </tr>

EOF;
                    else
                        $autoColumnEditStr .= <<<EOF
                            <tr>
                                <th style="VERTICAL-ALIGN: top">{$column_cn}：</th>
                                <td>$centerEditStr</td>
                            </tr>

EOF;
                    $i++;
                }
                $autoColumnEditStr = <<<EOF
<!--模型edit数据--><!--900--><!--该代码由系统自动生成-->
                        <table class="table-font">
$autoColumnEditStr
                        </table>
                        <!--模型edit数据 end-->
EOF;


                //list代码
                $i=0;
                $th_str = $td_str = '';

                //查找表的主键
                $column_primaryKey = null;
                foreach ($columns as $k=>$onecolumn)
                {
                    $column_key = $onecolumn['column_key'];
                    if ($column_key == 'PRI') {
                        $column_primaryKey = $onecolumn['column_name'];
                        //unset($columns[$k]);
                        break;
                    }
                }
                //查找不到主键的话就用第一个字段
                if (!$column_primaryKey)
                {
                    $column_primaryKey = $columns[0]['column_name'];
                }
                if (!$column_primaryKey)
                    throw new Exception("该表需要创建主键");

                //附加功能，精简时可删除
                $autoColumnListTopsearchHtml = '';
                $autoColumnListTopsearchJs = '';
                $autoColumnListTopsearchControl = ''; //列表头部ctl数据
                //附加功能，精简时可删除 end
                foreach ($columns as $k=>$onecolumn)
                {
                    $column_en = $onecolumn['column_name'];
                    $column_cn = $onecolumn['column_comment'] ? $onecolumn['column_comment'] : $column_en;
                    $column_type = $onecolumn['column_type']; //example: "enum('1','0')"
                    //该字段不需要生成
                    if ($column_en=='updated' || $column_en=='created')
                    {
                        continue;
                    }

                    //生成列表th
                    if (strpos($column_en, 'password') !== false) {
                        continue;
                    } else if ($column_en=='updated') {
                        $th_str .= '<th>修改时间</th>';
                    } else if ($column_en=='created') {
                        $th_str .= '<th>创建时间</th>';
                    } else if ($column_en=='displayorder') {
                        $th_str .= '<th>排序</th>';
                        //} else if ($column_en=='id') {
                        //    $th_str .= '<th width="41" class="text-center"><input type="checkbox" rel="control" onClick="this.checked?selCheckbox.Init(\'tb1\',\'del\',1):selCheckbox.Init(\'tb1\',\'del\',2);" /></th>';
                    } else {
                        $th_str .= '<th>'.$column_cn.'</th>';
                    }

                    //生成列表td
                    if ($column_en=='updated' || $column_en=='created' || strpos($column_en, 'time') !== false || strpos($column_cn, '时间戳') !== false) {
                        $td_str .= '<td>{if $v["'.$column_en.'"]}{eval echo date("Y-m-d H:i:s", $v["'.$column_en.'"])}{/if}</td>';
                    } else if ($column_en=='displayorder') {
                        $td_str .= '<td><input name="orderby[{$v["'.$column_primaryKey.'"]}]" type="text" id="orderby[{$v["'.$column_primaryKey.'"]}]" class="textinput" value="{$v["displayorder"]}"></td>';
                        //} else if ($column_en=='id') {
                        //    $td_str .= '<td class="text-center"><input name="id[]" type="checkbox" id="id[]" value="{$v["'.$column_primaryKey.'"]}" rel="del"  /></td>';
                        //} else if (strpos(strtolower($column_cn), 'json') !== false) {
                        //$td_str .= '<td>{if $v["'.$column_en.'"]}{eval echo date("Y-m-d H:i:s", $v["'.$column_en.'"])}{/if}</td>';
                    } else if (strpos($column_cn, '是否') !== false || $column_type == "enum('1','0')" || $column_type == "enum('0','1')" || $column_type == "enum('是','否')" || $column_type == "enum('否','是')") {
                        $td_str .= '<td><input type="checkbox" name="'.$column_en.'" id="{$v["'.$column_primaryKey.'"]}" class="btnSet" {if $v["'.$column_en.'"]==\'1\' || $v["'.$column_en.'"]==\'是\'}checked="checked"{/if} /></td>';
                    } else {
                        $td_str .= '<td>{$v["'.$column_en.'"]}</td>';
                    }

                    //生成列表头部的列表检索
                    //附加功能，精简时可删除
                    if (preg_match('/(enum)(.*)/i', $column_type)) {
                        preg_match_all('/\'(.*)\'/iU', $column_type, $match);
                        //$enumvalstr = '';
                        $enumvalarr = $match[1];
                        //$$enumvalstr = '';
                        $enumvalstr2 = '';
                        foreach ($enumvalarr as $enumval) {
                            $enumval = str_replace('\'', '', $enumval);
                            $enumval = str_replace('"', '', $enumval);
                            $enumvalstr .= '<option value="'.$enumval.'" {if $_GET["'.$column_en.'"]=='.$enumval.'}selected="selected"{/if}>'.$enumval.'</option>';
                            $enumvalstr2 .= $enumvalstr2 ? ",".$enumval : $enumval;
                        }
                        $autoColumnListTopsearchControl .= <<<EOF
            \$$column_en = isset(\$_GET["$column_en"]) && in_array(\$_GET["$column_en"], array($enumvalstr2)) ? \$_GET["$column_en"] : "";
            if (($$column_en = \$_GET['$column_en']) != "") //$column_cn 列表检索
            {
                \$conditions .= " AND $column_en = '$$column_en'";
                \$urls .= "&$column_en=$$column_en";
            }

EOF;
                        $autoColumnListTopsearchHtml .= <<<EOF
                            <select name="$column_cn" id="$column_en">
                                <option>--请选择$column_cn--</option>
                                $enumvalstr
                            </select>&nbsp;

EOF;

                        $autoColumnListTopsearchJs .= <<<EOF
<!--{$column_en}列表改变动作-->
<script type="text/javascript">
$(function() {
    $('#$column_en').change(function(){
        self.location.href='{:U("sample/list_index")}?$column_en='+$(this).val();
    });
});
</script>
<!--{$column_en}列表改变动作 end-->

EOF;

                    }
                    //生成列表头部的搜索数据
                    else if (strpos($column_en, 'name') !== false) {
                        $autoColumnListTopsearchControl .= <<<EOF
            if (($$column_en = \$_GET['$column_en']) != "") //$column_cn 列表检索
            {
                \$conditions .= " AND $column_en like '%$$column_en%'";
                \$urls .= "&$column_en=$$column_en";
            }

EOF;
                        $autoColumnListTopsearchHtml .= <<<EOF
                            <input type="text" id="$column_en" value="{\$_GET[$column_en]}" onclick="this.value=''" onblur="(this.value == '') ? this.value='快速搜索$column_cn':''" onmouseover="this.select()" value="快速搜索$column_cn" class="textinput gray9 w270" />

EOF;

                        $autoColumnListTopsearchJs .= <<<EOF
<!--autocomplete-->
<script type="text/javascript">
    $(function() {
        $("#{$column_en}").autocomplete({
            minLength: 2,
            //source: "{:U('sample/ajax_search')}",
            source: function( request, response ) {
                $.ajax({
                    url: "{:U('sample/ajax_search')}",
                    dataType: "json",
                    data: {
                        term: request.term,
                        fieldname: '{$column_en}'
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            focus: function(event, ui) {
                $('#{$column_en}').val(ui.item.value);
            },
            select: function(event, ui) {
                var message = ui.item ? ui.item.value : null;
                if (message) {
                    self.location.href = '{:U("sample/index?{$column_en}=")}'+encodeURI(message);
                }
            }
        })._renderItem = function( ul, item ) {
            //搜索结果中增加图像显示
            $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.value + "</a>" )
                .appendTo( ul );
        };
    });
</script>
<!--autocomplete end-->

EOF;
                    }
                    //附加功能，精简时可删除 end
                    $i++;
                }

                $autoColumnListStr = <<<EOF
                        <!--模型list数据--><!--906--><!--该代码由系统自动生成-->
                        <table class="admin-tb" id="tb1">
                            <tr>
                                <th width="41" class="text-center"><input type="checkbox" rel="control" onClick="this.checked?selCheckbox.Init('tb1','del',1):selCheckbox.Init('tb1','del',2);" /></th>
                                $th_str
                                <th>操作</th>
                            </tr>
                            {if \$data}
                            {loop \$data \$k \$v}
                            <tr>
                                <td class="text-center"><input name="id[]" type="checkbox" id="id[]" value="{\$v["$column_primaryKey"]}" rel="del"  /></td>
                                $td_str
                                <td><a href="{:U('$modname_en/edit')}?id={\$v["$column_primaryKey"]}">编辑</a></td>
                            </tr>
                            {/loop}
                            {/if}
                            <tr class="foot-ctrl">
                                <td colspan="$i+2" class="gray">选择: <a href="#" onClick="selCheckbox.Init('tb1','del',1);">全选</a> - <a href="#" onClick="selCheckbox.Init('tb1','del',3);">反选</a> - <a href="#" onClick="selCheckbox.Init('tb1','del',2);">无</a></td>
                            </tr>
                        </table>
                        <!--模型list数据 end-->

EOF;

                //附加功能，精简时可删除
                $autoColumnListTopsearchHtml = '<!--列表头部html数据--><!--905--><!--该代码由系统自动生成-->
'.$autoColumnListTopsearchHtml.'
                            <input type="submit" class="formbtn" value="搜索">
                            <!--列表头部html数据 end-->';
                $autoColumnListTopsearchJs = '<!--列表头部js数据--><!--904--><!--该代码由系统自动生成-->
'.$autoColumnListTopsearchJs.'
<!--列表头部js数据 end-->';
                $autoColumnListTopsearchControl = '<!--列表头部ctl数据--><!--903--><!--该代码由系统自动生成-->
'.$autoColumnListTopsearchControl.'
            //<!--列表头部ctl数据 end-->';
                //附加功能，精简时可删除 end

            }

            //判断模型文件是否存在， 存在的话要小心覆盖错误
            if (!$overwrite && file_exists($filename = PATH_CONTROLLER . "c_{$modname_en}.php"))
                throw new Exception("模型文件已存在.$filename");
            if (!$overwrite && file_exists($filename = PATH_MODEL . "m_{$modname_en}.php"))
                throw new Exception("模型文件已存在.$filename");
            if (!$overwrite && file_exists($filename = PATH_TPLS . "{$modname_en}/list.htm"))
                throw new Exception("模型文件已存在.$filename");
            if (!$overwrite && file_exists($filename = PATH_TPLS . "{$modname_en}/edit.htm"))
                throw new Exception("模型文件已存在.$filename");

            //开始生成相关文件
            if ($crtype=='cp_sample') //从sample新建
            {
                $content = file_get_contents(PATH_CONTROLLER . "c_sample.php");
                //附加功能，精简时可删除
                $content = preg_replace("|<!--列表头部ctl数据-->.*<!--列表头部ctl数据 end-->|isU", $autoColumnListTopsearchControl, $content); //<!--903-->
                //附加功能，精简时可删除 end
                $content = str_replace("sample", $modname_en, $content);
                $content = str_replace("示例", $modname_cn, $content);
                $content = str_replace("primary_id", $column_primaryKey, $content);
                cfile::write(PATH_CONTROLLER . "c_{$modname_en}.php", $content, "wb");

                $content = file_get_contents(PATH_MODEL . "m_sample.php");
                $content = str_replace("sample", $modname_en, $content);
                $content = str_replace("示例", $modname_cn, $content);
                $content = str_replace("primary_id", $column_primaryKey, $content);
                if ($dbconn) {
                    $content = str_replace("//{#dbconn}", '$this->db = dbmysqli::instance(\''.$dbconn.'\');', $content);
                } else {
                    $content = str_replace("//{#dbconn}\r\n", '', $content);
                }
                cfile::write(PATH_MODEL . "m_{$modname_en}.php", $content, "wb");

                $content = file_get_contents(PATH_TPLS . "sample/list.htm");
                //附加功能，精简时可删除
                $content = preg_replace("/<!--列表头部html数据-->.*<!--列表头部html数据 end-->/isU", $autoColumnListTopsearchHtml, $content); //<!--905-->
                $content = preg_replace("/<!--列表头部js数据-->.*<!--列表头部js数据 end-->/isU", $autoColumnListTopsearchJs, $content); //<!--904-->
                //附加功能，精简时可删除 end
                $content = str_replace("sample", $modname_en, $content);
                $content = str_replace("示例", $modname_cn, $content);
                if ($crcolumn)
                    $content = preg_replace("/<!--模型list数据-->.*<!--模型list数据 end-->/isU", $autoColumnListStr, $content); //<!--906-->
                if (!file_exists(PATH_TPLS . "{$modname_en}"))
                    mkdir(PATH_TPLS . "{$modname_en}", 0755);
                cfile::write(PATH_TPLS . "{$modname_en}/list.htm", $content, "wb");

                $content = file_get_contents(PATH_TPLS . "sample/edit.htm");
                $content = str_replace("sample", $modname_en, $content);
                $content = str_replace("示例", $modname_cn, $content);
                if ($crcolumn)
                    $content = preg_replace("/<!--模型edit数据-->.*<!--模型edit数据 end-->/isU", $autoColumnEditStr, $content); //<!--900-->
                cfile::write(PATH_TPLS . "{$modname_en}/edit.htm", $content, "wb");
            }
            else //复制模型
            {
                $content = file_get_contents(PATH_CONTROLLER . "c_{$model_source_en}.php");
                $content = str_replace($model_source_en, $modname_en, $content);
                foreach ($model_source_replace as $one)
                {
                    $strarr = explode("-", $one);
                    $content = str_replace($strarr[0], $strarr[1], $content);
                }
                cfile::write(PATH_CONTROLLER . "c_{$modname_en}.php", $content, "wb");

                $content = file_get_contents(PATH_MODEL . "m_{$model_source_en}.php");
                $content = str_replace($model_source_en, $modname_en, $content);
                foreach ($model_source_replace as $one)
                {
                    $strarr = explode("-", $one);
                    $content = str_replace($strarr[0], $strarr[1], $content);
                }
                cfile::write(PATH_MODEL . "m_{$modname_en}.php", $content, "wb");

                $content = file_get_contents(PATH_TPLS . "{$model_source_en}/list.htm");
                $content = str_replace($model_source_en, $modname_en, $content);
                foreach ($model_source_replace as $one)
                {
                    $strarr = explode("-", $one);
                    $content = str_replace($strarr[0], $strarr[1], $content);
                }
                if ($crcolumn)
                    $content = preg_replace("/<!--模型list数据-->.*<!--模型list数据 end-->/isU", $autoColumnListStr, $content);
                if (!file_exists(PATH_TPLS . "{$modname_en}"))
                    mkdir(PATH_TPLS . "{$modname_en}", 0755);
                cfile::write(PATH_TPLS . "{$modname_en}/list.htm", $content, "wb");

                $content = file_get_contents(PATH_TPLS . "{$model_source_en}/edit.htm");
                $content = str_replace($model_source_en, $modname_en, $content);
                foreach ($model_source_replace as $one)
                {
                    $strarr = explode("-", $one);
                    $content = str_replace($strarr[0], $strarr[1], $content);
                }
                if ($crcolumn)
                    $content = preg_replace("/<!--模型edit数据-->.*<!--模型edit数据 end-->/isU", $autoColumnEditStr, $content);
                cfile::write(PATH_TPLS . "{$modname_en}/edit.htm", $content, "wb");
            }
            //记录生成的文件
            $this->created_file['控制器文件'] = PATH_CONTROLLER . "c_{$modname_en}.php";
            $this->created_file['模型文件'] = PATH_MODEL . "m_{$modname_en}.php";
            $this->created_file['模板（列表）'] = PATH_TPLS . "{$modname_en}/list.htm";
            $this->created_file['模板（编辑）'] = PATH_TPLS . "{$modname_en}/edit.htm";
            foreach ($this->created_file as $k=>$v)
            {
                $msgfiles .= "$k: $v<br>";
            }

            $this->message("创建模型完毕.<br>".$modname_cn.$modname_en."<br>".$msgfiles, null, 1, 1);

        }
        catch( Exception $e )
        {
            $this->view->assign('error', $e->getMessage());
            //$this->message("出错了.<br>".$e->getMessage()."<br>", null, 100);
            $this->show_createmodel();
        }
    }


}