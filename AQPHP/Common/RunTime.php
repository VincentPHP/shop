<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/

//运行时间
function runTime()
{   
    $Files = require_once PHP_PATH.'/Common/Files.php';
 
    foreach ($Files as $v)
    {
		if(is_file($v))	require $v;	
    }
    
    C(require PHP_PATH.'/Libs/Etc/Init.Config.php'); //框架常规配置项
   	
    mkDirs();
    
    $Data = '';
    foreach($Files as $v)
    {
       $Data .= DelSpace($v);
    }
	
    $Data = '<?php'.$Data."C(require PHP_PATH.'/Libs/Etc/Init.Config.php'); ?>";
   
    file_put_contents(TEMP_PATH.'/RunTime.php',$Data);//生成运行代码总和文件
    
    $ModuleDir = MODULE_PATH.'/'.C('DEFAULT_MODULE');//默认模块路径
    
    if(!is_dir($ModuleDir))
    {
       IndexControl();//框架演示函数
    }
}

//创建测试页面
function IndexControl(){

    $IndexDir     = MODULE_PATH.'/'.C('DEFAULT_MODULE');  //组合默认模块 
    $IndexControl = C('DEFAULT_CONTROL').C('CONTROL_FIX');//组合默认控制器
    $IndexAction  = C('DEFAULT_ACTION');//默认方法
    $ActionDir = ucfirst($IndexAction); //获取路径
    
$Code[0] = <<<CODE
<?php
namespace Home\Controller;
use AQPHP\Libs\Controller;

Class $IndexControl extends Controller{
    
    public function $IndexAction()
    {
        \$this->Display();
    }
}
CODE;
    
$Code[1] = <<<str
<div style='border:1px solid #dcdcdc;width:350px;height:40px;padding:20px'>
    <h1 style='color:#666;text-align:center;display:inline;'>欢迎使用AQPHP框架</h1>
</div>
str;
    
    //创建MVC结构目录
    $DefaultDir = array();
	$Arry = array('Controller','Model','View','Conf');
	
    for($i=0; $i<count($Arry); $i++)
    {
        $DefaultDir[] .= $IndexDir.'/'.$Arry[$i];

        if(!is_dir($DefaultDir[$i]))  mkdir($DefaultDir[$i], 0777, true);
    }
    
    $IndexFile  = $IndexAction.C('ACTION_FIX'); //默认文件
    
    //组合控制器路径
    $FileDir[0] = $IndexDir.'/Controller/'.$IndexControl.C('CLASS_FIX').'.php';
    
    //组合模板路径
    if(!C('START_TPLDIR'))
    {
        $ACTION_FILE = "{$IndexDir}/View/{$ActionDir}";//View模板路径
    }
    else
    {
        $ACTION_FILE = C('TEMPLETE_PATH').'/'.C('DEFAULT_MODULE').'/'.C('DEFAULT_CONTROL'); //Templete模板路径
    }
    
    $FileDir[1]  = $ACTION_FILE.'/'.$IndexFile; //方法模板文件名
    
    if(C('START_TPLDIR') && !is_dir($ACTION_FILE)) mkdir($ACTION_FILE, 0777, true); //创建模板目录
    
    for($i=0; $i<count($FileDir); $i++)
    {
        if(!is_file($FileDir[$i]))
        {
            file_put_contents($FileDir[$i], $Code[$i]); //创建默认演示文件
        }     
    }
}

//创建环境目录
function mkDirs()
{
   if(!is_dir(TEMP_PATH))
   {
       mkdir(TEMP_PATH, 0777); //判断目录是否存在
   }
   
   if(!is_writable(TEMP_PATH))
   {
       error("目录没有写入权限，程序无法运行");//检测目录是否有写入权限
   }

   //检测目录是否存在 不存在则创建
   $Path = array(
            CACHE_PATH,LOG_PATH,TPL_PATH,
            CONFIG_PATH,UPLOAD_PATH,
            C('PUBLIC_PATH'),C('TEMPLETE_PATH'));

   for($i=0; $i<count($Path); $i++)
   {
   	    if(!is_dir($Path[$i])) mkdir($Path[$i], 0777, true);
   }
}