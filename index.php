<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/6 0006
 * Time: 9:20
 */

define('DOCROOT', realpath('./').'/');
define('PATH_ROOT', DOCROOT);
define('PATH_TOOL', PATH_ROOT.'tools/');
define('PATH_MODEL', PATH_ROOT.'model/');
define('PATH_CONTROLLER', PATH_ROOT.'controller/');
define('PATH_TPLS', PATH_ROOT.'view/default/');
define('DEBUG_LEVEL', true);
define('URL', dirname($_SERVER['SCRIPT_NAME']).'/'); // 例：/table2form/
define('PAGE_ROWS', 8); //分页


$tmp = explode('/', substr($_SERVER['PATH_INFO'], 1));
$c = isset($tmp[0]) && !empty($tmp[0]) ? $tmp[0] : (isset($_GET['c']) ? $_GET['c'] : 'createmodel');
$a = isset($tmp[1]) && !empty($tmp[1]) ? $tmp[1] : (isset($_GET['a']) ? $_GET['a'] : 'index');

/**
 * 动态加载类
 */
spl_autoload_register('autoload', '.php');

/**
 * 加载配置
 */
class glb {
    public static $config;
}
require(PATH_ROOT . 'config/config.php');
glb::$config = $config;

$control_name = "c_{$c}";
$control = new $control_name();
if (!method_exists($control, $a)) {
    dir("方法未定义:$c::$a");
}
if (method_exists($control, 'pre')) {
    $control->pre();
}
$result = $control->$a();
if (method_exists($control, 'post')) {
    $control->post();
}


/**
 * 动态加载类
 */
function autoload($classname)
{
    if (!class_exists($classname, false))
    {
        if (substr($classname, 0, 2) == 'm_') //模型
        {
            include_once(PATH_ROOT.'include/base.php');
            include(PATH_ROOT.'include/base_model.php');
            $classfile = $classname . '.php';
            if (is_file(PATH_MODEL . $classfile))
            {
                require_once(PATH_MODEL . $classfile);
            }
            else
            {
                throw new Exception('找不到模型 ' . $classname);
            }
        }
        else if (substr($classname, 0, 2) == 'c_') //控制器
        {
            include_once(PATH_ROOT.'include/base.php');
            include(PATH_ROOT.'include/base_controller.php');
            $classfile = $classname . '.php';
            if (is_file(PATH_CONTROLLER . $classfile))
            {
                require_once(PATH_CONTROLLER . $classfile);
            }
            else
            {
                throw new Exception('找不到控制器 ' . $classname);
            }
        }
        else //工具类
        {
            $classfile = $classname . '.php';
            if (is_dir(PATH_TOOL . $classname) && is_file(PATH_TOOL . $classname . '/' . $classfile))
            {
                require_once(PATH_TOOL . $classname . '/' . $classfile);
            }
            else if (is_file(PATH_TOOL . $classfile))
            {
                require_once(PATH_TOOL . $classfile);
            }
            else if (is_file(PATH_TOOL . $classname . '.class.php'))
            {
                require_once(PATH_TOOL . $classname . '.class.php');
            }
            else
            {
                throw new Exception('找不到工具类 ' . $classname);
            }
        }

    }
}

function U($s)
{
    if (substr($s, 0, 1) != '/') {
        $s = '/'.$s;
    }
    if (substr($s, 0, 10) != $_SERVER['SCRIPT_NAME']) {
        $s = $_SERVER['SCRIPT_NAME'].$s;
    }
    return $s;
}