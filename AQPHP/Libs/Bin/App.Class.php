<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/

namespace AQPHP\Libs\Bin;

use AQPHP\Libs\DeBug;
use AQPHP\Libs\Log;
use AQPHP\Libs\Url;

/**
 * App 框架核心类
 * @author Vincent
 * @package AQPHP\Libs\Bin
 */

Class App
{
    static function run()
    {
        spl_autoload_register(array(__CLASS__, "AutoLoad"));//配置自动加载类文件
        
        set_error_handler(array(__CLASS__, "Error"));       //注册错误处理方法
        
        set_exception_handler(array(__CLASS__, "Exception"));//注册异常处理方法
        
        //是否转义
        define("MAGIC_QUOTES_GPC", get_magic_quotes_gpc() ? true : false);
        
        //设置时区
        if(function_exists('date_default_timezone_set'))
        {
            date_default_timezone_set(C("DATE_TIMEZONE_SET"));
        }
        
        session_id() || session_start(); //开启SESSION
        
        self::Config();//初始化配置
        
        //调试开始
        if(C("DEBUG"))
        {
            DeBug::Start("App_Start");
        }
        
        //初始化框架
        self::Init(); 
        
        //开启调试
        if(C("DEBUG"))
        {
            DeBug::Show("App_Start","App_End");
        }
        
        //日志存储
        Log::Save();
    }
    
    
    /**
     * 初始化配置
     */
    static function Init()
    {
       Url::ParseUrl();//调用路由

       $Control = A(MODULE.'.'.CONTROL);
       
       $Action  = ACTION;
 
       if(!method_exists($Control,$Action))
       {
           Error('[ '.CONTROL.C('CONTROL_FIX')." ]控制器中的[ {$Action} ]动作不存在");
       }
       
       $Control->$Action();
    }
  
    
    /**
     * 初始化配置文件处理
     */
    static function Config()
    {    
        $config_file = CONFIG_PATH.'/Config.php';
        
        if(is_file($config_file))
        {
            C(require $config_file);
        }   
    }



    /**
     * 异常处理
     * @param unknown $Error
     */
    static function Exception($Exception)
    {
        Error($Exception);
    }
    
    
    /**
     * 显示加载错误
     * @param unknown $ErrNo
     * @param unknown $ErrStr
     * @param unknown $ErrFile
     * @param unknown $ErrLine
     */
    static function Error($ErrNo,$ErrStr,$ErrFile,$ErrLine)
    {
        switch ($ErrNo)
        {
            case E_ERROR:
    
            case E_USER_ERROR:
    
                //错误信息
                $ErrMsg = "ERROR:[{$ErrNo}]<strong> {$ErrStr} </strong> <br/>File:{$ErrFile}[{$ErrLine}]";
    
                //进行日志写入
                Log::Write("ERROR:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}[{$ErrLine}]");
    
                //提示错误
                Error($ErrMsg);
            break;
    
            case E_NOTICE:
    
            case E_WARNING:
    
            default:
    
                //错误信息
                $ErrMsg = "NOTICE:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}"."[{$ErrLine}]";
    
                //进行日志记录
                Log::Set("NOTICE:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}[{$ErrLine}]","NOTICE");
                 
                //提示错误信息
                Notice(func_get_args());
            break;
        }
    }
 

    /**
     * 自动加载类文件
     * @param String $ClassName 类名
     */
    static function AutoLoad($ClassName)
    {
        $StrArr = array('Libs','Common\Controller');
        
        for ($i=0; $i<count($StrArr); $i++)
        {
            if(strstr($ClassName, $StrArr[$i]))
            {
                $ClassFile = $ClassName.C('CLASS_FIX').'.php';
               
                if(strstr($ClassName, $StrArr[1]))
                {
                    $ClassFile = MODULE_PATH.'/'.$ClassFile;
                }
                    
                if(file_exists($ClassFile)) LoadFile($ClassFile); //载入文件
            }
            else
            {
                $ClassFile   = $ClassName.'.php';
                
                if(file_exists($ClassFile)) LoadFile($ClassFile); //载入文件
            }
        }
    }
}