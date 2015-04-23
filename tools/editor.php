<?php
/**
 * fckeditor编辑器
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */
require_once("fckeditor/fckeditor.php") ;

/**
 * 
 * 		使用示例：
			$editor = new editor('Mine', 'white', 'zh-cn');
			$editor->create($cf_name, $cf_value);		
			
			or
			$editor = new editor('Mine', 'white', 'zh-cn');
			$editor->setSimple();
			$cont = $editor->createHtml($cf_name, $cf_value);
			echo $cont;
 */

class editor
{
	var $editor_type;
	var $cf_lang;
	var $cf_skin;
	var $cf_toolbar; 
	var $width; 
	var $height; 
	var $simple; 
	
	/**
	 * editor构造函数
	 *
	 * @param string $sToolbar 可用值：Basic,Default,Mine,MineSmall (Mine定义在mine.config.js)
	 * @param string $sSkin 可用值：default, office2003, silver, mac, white
	 * @param string $sLang 可用值：zh-cn, zh, en...
	 */
	public function __construct($sToolbar='Default', $sSkin='default', $sLang='en')
	{
		$this->cf_toolbar = $sToolbar;
		$this->cf_skin = $sSkin;
		$this->cf_lang = $sLang;
		$this->width = 660;
		$this->height = 300;
		$this->simple = false;
	}
	
	public function create($editor_name, $editor_value, $width='', $height='')
	{
		$oFCKeditor = $this->_config($editor_name, $editor_value, $width, $height);
		$oFCKeditor->Create() ;
	}

	public function createHtml($editor_name, $editor_value, $width='', $height='')
	{	
		$oFCKeditor = $this->_config($editor_name, $editor_value, $width, $height);
		return $oFCKeditor->CreateHtml() ;
	}
	
	/**
	 * 精简版
	 */
	public function setSimple()
	{
		$this->simple = true;
	}
	
	public function _config($editor_name, $editor_value, $width, $height)
	{	
		$editor_name = $editor_name ? $editor_name :'FCKeditor1';
		$editor_value = $editor_value ? $editor_value : '';
		// $oFCKeditor->BasePath = '/fckeditor/' ;	// '/fckeditor/' is the default value.
	//	$sBasePath = URL . 'lib/editors/fckeditor/';
		//$sBasePath = VIRTUAL_DIR.'lib/editors/fckeditor/';
		$sBasePath = '/table2form/tools/fckeditor/';
		
		global $Config;
		$Config['UserFilesPath'] = PATH_ROOT . 'data/upload/fckeditor/' ;
		$oFCKeditor = new FCKeditor($editor_name);
		$oFCKeditor->BasePath = $sBasePath;
				
		if ($width)
		$this->width = $width;
		if ($height)
		$this->height = $height;
		
		$oFCKeditor->Width = $this->width;
		$oFCKeditor->Height = $this->height;

		//工具栏设置
		if ( $this->cf_toolbar )
		{
			// Set the custom configurations file path (in this way the original file is mantained).
			if ($this->simple==false)
			$oFCKeditor->Config['CustomConfigurationsPath'] = $sBasePath . 'mine.config.js' ;
			else
			$oFCKeditor->Config['CustomConfigurationsPath'] = $sBasePath . 'mine.simple.config.js' ;
			// Let's use a custom toolbar for this sample.
			$oFCKeditor->ToolbarSet = $this->cf_toolbar;
		}
		//皮肤选择
		if ( $this->cf_skin )
		{
			$oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/'.$this->cf_skin.'/' ;
		}
		//语言选择
		if ( $this->cf_lang )
		{
			$oFCKeditor->Config['AutoDetectLanguage']	= false ;
			$oFCKeditor->Config['DefaultLanguage']		= $this->cf_lang;
		}
		else
		{
			$oFCKeditor->Config['AutoDetectLanguage']	= true ;
			$oFCKeditor->Config['DefaultLanguage']		= 'en' ;
		}
		//默认展开工具栏
		$oFCKeditor->Config['ToolbarStartExpanded'] = true;

		$oFCKeditor->Value = $editor_value ;
		return 	$oFCKeditor;
	}
}
?>