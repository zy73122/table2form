<?php
/*
 * [UAP Server] (C)1999-2009 ND Inc.
 * $Id: template.class.php 199495 2011-12-09 09:30:16Z chenguo $
 */

class template {

    var $tpldir;
    var $objdir;

    var $tplfile;
    var $objfile;
    var $langfile;

    var $vars;
    var $force = 0;

    var $var_regexp = "\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*";
    var $vtag_regexp = "\<\?=(\@?\\\$[a-zA-Z_]\w*(?:\[[\w\.\"\'\[\]\$]+\])*)\?\>";
    var $const_regexp = "\{([\w]+)\}";

    var $languages = array();
    var $sid;

    function __construct() {
        $this->template();
    }

    function template() {
        ob_start();
        $this->defaulttpldir = DOCROOT.'./view/default';
        $this->tpldir = DOCROOT.'./view/default';
        $this->objdir = DOCROOT.'./view/data';
        $this->langfile = DOCROOT.'./view/default/templates.lang.php';
        if (version_compare(PHP_VERSION, '5') == -1) {
            register_shutdown_function(array(&$this, '__destruct'));
        }
    }

    function assign($k, $v) {
        $this->vars[$k] = $v;
    }

    function display($file) {
    	if ($this->vars) extract($this->vars, EXTR_SKIP);
        include $this->gettpl($file);
    }

    function gettpl($file) {
        isset($_REQUEST['inajax']) && ($file == 'header' || $file == 'footer') && $file = $file.'_ajax';
        isset($_REQUEST['inajax']) && ($file == 'admin_header' || $file == 'admin_footer') && $file = substr($file, 6).'_ajax';
        $this->tplfile = $this->tpldir.'/'.$file.'.htm';
        $this->objfile = $this->objdir.'/'.$file.'.php';
        $tplfilemtime = @filemtime($this->tplfile);
        if($tplfilemtime === FALSE) {
            $this->tplfile = $this->defaulttpldir.'/'.$file.'.htm';
        }
        if($this->force || !file_exists($this->objfile) || @filemtime($this->objfile) < filemtime($this->tplfile)) {
            if(empty($this->language)) {
                @include $this->langfile;
                if(is_array($languages)) {
                    $this->languages += $languages;
                }
            }
            $this->complie();
        }
        //$this->complie();
        return $this->objfile;
    }

    function complie() {
        $template = file_get_contents($this->tplfile);


        //模板注释byy   <!---输出HTML代码时会被清空的注释-->
        //$template = preg_replace("/\/\/[^\n]*\n/is", "", $template);
        //$template = preg_replace("|<!-- \#([^\n]+)-->\s*\r?\n|i", "", $template);
        $template = preg_replace("/[ \t]*\<\!--\#.+-->/i", "", $template);
        //$template = preg_replace("/[ \t]+\<\!--/i", "<!--", $template);  // 去掉<!--前面的空格
        $template = preg_replace("/\s+\n/i", "\n", $template);  // 去掉空行
        $template = preg_replace("/[ \t]+\{/i", "{", $template); // 取掉 { 之前的空格
        //<php>..</php>  byy
        $template = preg_replace("/\<php\>(.+)\<\/php\>/isU", "<?php \\1?>", $template);


        $template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
        $template = preg_replace("/\{lang\s+(\w+?)\}/ise", "\$this->lang('\\1')", $template);

        $template = preg_replace("/\{($this->var_regexp)\}/", "<?=\\1?>", $template);
        $template = preg_replace("/\{($this->const_regexp)\}/", "<?=\\1?>", $template);
        $template = preg_replace("/(?<!\<\?\=|\\\\)$this->var_regexp/", "<?=\\0?>", $template);

        $template = preg_replace("/\<\?=(\@?\\\$[a-zA-Z_]\w*)((\[[\\$\[\]\w]+\])+)\?\>/ies", "\$this->arrayindex('\\1', '\\2')", $template);

        $template = preg_replace("/\{\{eval (.*?)\}\}/ies", "\$this->stripvtag('<?php \\1?>')", $template);
        $template = preg_replace("/\{eval (.*?)\}/ies", "\$this->stripvtag('<?php \\1?>')", $template);

        $template = preg_replace("/\{:(.*?)\}/ies", "\$this->stripvtag('<?php echo \\1?>')", $template);//{:fun(..)}

        $template = preg_replace("/\{for (.*?)\}/ies", "\$this->stripvtag('<?php for(\\1) {?>')", $template);

        $template = preg_replace("/\{elseif\s+(.+?)\}/ies", "\$this->stripvtag('<?php } elseif(\\1) { ?>')", $template);

        for($i=0; $i<2; $i++) {
            $template = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '\\2', '\\3', '\\4')", $template);
            $template = preg_replace("/\{loop\s+$this->vtag_regexp\s+$this->vtag_regexp\}(.+?)\{\/loop\}/ies", "\$this->loopsection('\\1', '', '\\2', '\\3')", $template);
        }
        $template = preg_replace("/\{if\s+(.+?)\}/ies", "\$this->stripvtag('<?php if(\\1) { ?>')", $template);

        $template = preg_replace("/\{template\s+(\w+?)\}/is", "<?php include \$this->gettpl('\\1');?>", $template);
        $template = preg_replace("/\{template\s+(.+?)\}/ise", "\$this->stripvtag('<?php include \$this->gettpl(\\1); ?>')", $template);


        $template = preg_replace("/\{else\}/is", "<?php } else { ?>", $template);
        $template = preg_replace("/\{\/if\}/is", "<?php } ?>", $template);
        $template = preg_replace("/\{\/for\}/is", "<?php } ?>", $template);

        $template = preg_replace("/$this->const_regexp/", "<?=\\1?>", $template);
 
        $template = preg_replace("/\<\?=/is", "<?php echo ", $template);
        $template = preg_replace("/(\\\$[a-zA-Z_]\w+\[)([a-zA-Z_]\w+)\]/i", "\\1'\\2']", $template);

        umask(002);
        if (!file_exists(dirname($this->objfile))) {
            mkdir(dirname($this->objfile), 0755, true);
        }
        $fp = fopen($this->objfile, 'w');
        fwrite($fp, $template);
        fclose($fp);
    }

    function arrayindex($name, $items) {
        $items = preg_replace("/\[([a-zA-Z_]\w*)\]/is", "['\\1']", $items);
        return "<?=$name$items?>";
    }

    function stripvtag($s) {
        return preg_replace("/$this->vtag_regexp/is", "\\1", str_replace("\\\"", '"', $s));
    }

    function loopsection($arr, $k, $v, $statement) {
        $arr = $this->stripvtag($arr);
        $k = $this->stripvtag($k);
        $v = $this->stripvtag($v);
        $statement = str_replace("\\\"", '"', $statement);
        return $k ? "<?php foreach((array)$arr as $k => $v) {?>$statement<?php }?>" : "<?php foreach((array)$arr as $v) {?>$statement<?php } ?>";
    }

    function lang($k) {
        return !empty($this->languages[$k]) ? $this->languages[$k] : "{ $k }";
    }

    function _transsid($url, $tag = '', $wml = 0) {
        $sid = $this->sid;
        $tag = stripslashes($tag);
        if(!$tag || (!preg_match("/^(http:\/\/|mailto:|#|javascript)/i", $url) && !strpos($url, 'sid='))) {
            if($pos = strpos($url, '#')) {
                $urlret = substr($url, $pos);
                $url = substr($url, 0, $pos);
            } else {
                $urlret = '';
            }
            $url .= (strpos($url, '?') ? ($wml ? '&amp;' : '&') : '?').'sid='.$sid.$urlret;
        }
        return $tag.$url;
    }

    function __destruct() {
        return;
        if(isset($_COOKIE['sid']) && $_COOKIE['sid']) {
            return;
        }
        $sid = rawurlencode($this->sid);
        $searcharray = array(
            "/\<a(\s*[^\>]+\s*)href\=([\"|\']?)([^\"\'\s]+)/ies",
            "/(\<form.+?\>)/is"
        );
        $replacearray = array(
            "\$this->_transsid('\\3','<a\\1href=\\2')",
            "\\1\n<input type=\"hidden\" name=\"sid\" value=\"".rawurldecode(rawurldecode(rawurldecode($sid)))."\" />"
        );
        $content = preg_replace($searcharray, $replacearray, ob_get_contents());
        ob_end_clean();
        echo $content;
    }

}

/*

Usage:
require_once 'lib/template.class.php';
$this->view = new template();
$this->view->assign('page', $page);
$this->view->assign('userlist', $userlist);
$this->view->display("user_ls");

 */

?>
