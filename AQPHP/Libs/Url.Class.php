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
 * 路由处理类
 * @author Vincent
 */
 
Final Class Url
{
    /**
     * 保存PATHINFO信息
     * @var String $PathInfo
     */
    static $PathInfo;   
    
    /**
     * 解析URL
     */ 
    static function ParseUrl()
    {
       if(self::PathInfo() != FALSE)
       {
          $Info = explode(C('PATHINFO_DLI'),self::$PathInfo);
          
          if($Info[0] != C('VAR_MODULE'))
          {
              $Get['m'] = $Info[0];
              array_shift($Info);
              
              $Get['c'] = $Info[0];
              array_shift($Info);
              
              $Get['a'] = $Info[0];
              array_shift($Info);
          }
          
          for($i=0;$i<count($Info);$i+=2)
          {
              $Get[$Info[$i]] = $Info[$i+1];
          }
          
          $_GET = $Get;
          
       }

       define("MODULE", isset($_GET['m']) && !empty($_GET['m']) ? $_GET['m']:C('DEFAULT_MODULE'));
       define("CONTROL",isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c']:C('DEFAULT_CONTROL'));
       define("ACTION", isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a']:C('DEFAULT_ACTION'));

    }
    
    /**
     * 解析PATHINFO
     */
    static function PathInfo()
    {
        //获得PATHINFO变量
        if(!empty($_GET[C('PATHINFO_VAR')]))
        {
            $PathInfo = $_GET[C('PATHINFO_VAR')];
        }
        else if(!empty($_SERVER['PATH_INFO']))
        {
            $PathInfo = $_SERVER['PATH_INFO'];
        }
        else
        {
            return FALSE;
        }
        
        $PathInfo_FIX = '.'.trim(C('PATHINFO_FIX'),'.');
        $PathInfo = str_ireplace($PathInfo_FIX, '', $PathInfo);
        $PathInfo = trim($PathInfo, '/');
        
        if(stripos($PathInfo, C('PATHINFO_DLI')) == FALSE)
        {
            return FALSE;
        }
        
        self::$PathInfo = $PathInfo;
        return TRUE;
    }
    
}