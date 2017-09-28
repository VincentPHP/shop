<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/
namespace AQPHP\Libs;

/**
 * 结合View模板类结合SMARTY
 * @author Vincent
 */

//引入Smarty
include PHP_ORG_PATH . '/smarty/libs/Smarty.class.php';

Class View{
    
    private static $Smarty = null;
    private $TplDir;
    
    public function __construct()
    {
        if(!is_null(self::$Smarty)) return ;
        
        $Smarty = new \Smarty(); //实例化Smarty对象
        
        $Dir = array(
            'ComDir'=>TPL_PATH.'/'.MODULE,
            'CacheDir'=>CACHE_PATH.'/'.MODULE,
        );
        
        foreach($Dir as $v)
        {
            is_dir($v) || mkdir($v, 0777, true);
        }
        
        $ViewTpl = MODULE_PATH.'/'.MODULE.'/View/'.CONTROL; //View模板目录
        $TempleteTpl = C('TEMPLETE_PATH').'/'.MODULE.'/'.CONTROL; //Templete模板目录
            
        $this->TplDir = C('START_TPLDIR') ? $TempleteTpl : $ViewTpl ;
        
        $Smarty->template_dir = $this->TplDir;  //模板目录
        $Smarty->compile_dir  = $Dir['ComDir']; //编译目录
        $Smarty->cache_dir = $Dir['CacheDir'];  //缓存目录
        $Smarty->caching = C('SMARTY_CACHING'); //是否缓存
        $Smarty->cache_lifetime = C('SMARTY_LIFETIME');//缓存时间
        $Smarty->left_delimiter = C('SMARTY_LEFT'); //开始定界符
        $Smarty->right_delimiter= C('SMARTY_RIGHT');//结束定界符
        
        //$Smarty->setCaching(\Smarty::CACHING_LIFETIME_CURRENT);
        //$Smarty->registerPlugin('block','dynamic', 'smarty_block_dynamic', false);//局部不缓存        

        self::$Smarty = $Smarty; //存入静态属性
    }
    
    protected function Display()
    {
    	$Path = $this->TplDir.'/'.ACTION.C('ACTION_FIX');    		

        if(!is_file($Path)) Error('[ '.$Path.' ] 该模板文件不存在');
        
        self::$Smarty->display(ACTION.C('ACTION_FIX'), $_SERVER['REQUEST_URI']);
		exit();
    }
    
    
    protected function Assign($Name,$Value)
    {
        self::$Smarty->assign($Name, $Value);
    }
    
	
	protected function Success($Msg, $Url='', $Time=3)
	{
	    $Data = array('msg'=>$Msg, 'url'=>$Url, 'time'=>$Time);
	    
	    self::$Smarty->assign('data', $Data);
		self::$Smarty->display(C("SUCCESS_TPL"));
	}
	
	
	protected function Error($Msg, $Url='', $Time=3)
	{
		$Data = array('msg'=>$Msg, 'url'=>$Url, 'time'=>$Time);
	    		
	    self::$Smarty->assign('data', $Data);
		self::$Smarty->display(C("ERROR_TPL"));
		exit;	
	}
	
    protected function Is_Cached()
    {
        return self::$Smarty->is_cached(ACTION.'.html', $_SERVER['REQUEST_URI']);
    }
    
    protected function Clear_Cache()
    {
        self::$Smarty->clear_cache();
    }
    
	protected function smarty_block_dynamic($param, $content, $smarty)
	{
    	return $content;
	}
}