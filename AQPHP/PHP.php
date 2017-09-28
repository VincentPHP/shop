<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/

/**
 * 计算运行时间
 * @param string $start 开始时间
 * @param string $end   结束时间
 * @param int $decimial 显示几位数
 * @return int 总消耗时间
 */
function run_Time($start, $end='', $decimial=3)
{
    static $times = array();
    
    if($end != '')
    {
        $times[$end] = microtime();

        return number_format($times[$end] - $times[$start], $decimial);
    }

    $times[$start] = microtime();
}

run_Time("start");  //记录开始运行时间

//项目初始化
if(!defined("APP_PATH"))
{
    define("APP_PATH", dirname($_SERVER['SCRIPT_FILENAME']));    
}

define("PHP_PATH", dirname(__FILE__));   //框架主目录
define("TEMP_PATH", APP_PATH.'/Temp');        //临时目录

$runTime_file = TEMP_PATH.'/RunTime.php11';   //加载编译文件

if(is_file($runTime_file))
{
    require $runTime_file; 
}
else
{	
    include PHP_PATH.'/Common/RunTime.php';
    runTime(); 
}

AQPHP\Libs\Bin\App::run();//运行项目

run_Time("end"); //记录结束运行时间
