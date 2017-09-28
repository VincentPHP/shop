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
 * DEBUG 调试类
 * @author Vincent
 */

Class DeBug{
   
   static $runTime;      //运行时间
   
   static $memory;       //内存占用
 
   static $memory_peak;  //内容峰值
   
   /**
    * 调试开始
    * @param unknown $start
    */
   static function Start($start)
   {
       self::$runTime[$start]     = microtime(true);
       self::$memory[$start]      = memory_get_usage();
       self::$memory_peak[$start] = memory_get_peak_usage();
   }
   
   
   /**
    * 运行时间
    * @param unknown $start
    * @param string $end
    * @param number $decimals
    */
   static function runTime($start,$end='',$decimals=5)
   {
       if(!isset(self::$runTime[$start]))
       {
           Error('必须设置项目起点');
       }
       
       if(empty(self::$runTime[$end]))
       {
           self::$runTime[$end] = microtime(true);//获取结束时间 
           return number_format((self::$runTime[$end] - self::$runTime[$start]),$decimals);
       }
   }
   
   
   /**
    * 内存占用峰值
    * @param unknown $start
    * @param string $end
    */
   static function Memory_peak($start,$end='')
   {
       if(!isset(self::$memory_peak[$start]))
       {
           return false;
       }
       
       if(!empty($end))
       {
           self::$memory_peak[$end] = memory_get_peak_usage();
       }
       
       return max(self::$memory_peak[$start],self::$memory_peak[$end]);
   }
   
   
   /**
    * 项目运行结果
    * @param unknown $start
    * @param unknown $end
    */
   static function Show($start,$end)
   {
       $Message = "运行时间:".self::runTime($start,$end)."&nbsp;&nbsp;内存峰值:".number_format(self::Memory_peak($start,$end)/1024).'KB'; 
       
       $load_file_list = loadFile();
       
       $info ='';
       $i = 1;
       foreach($load_file_list as $k=>$v)
       {
           $info .= '['.$i++.']'.$k.'<br/>';
       }
       
       $E['info'] = $info."<p>$Message</p>";
       
       include C("DEBUG_TPL");
   }
}