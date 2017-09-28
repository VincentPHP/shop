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
 * 系统运行日志类
 * @author Vincent
 */
 
Class Log{
    
   static $Log = array();
   
   /**
    * 记录日志内容
    * @param unknown $Message 日志信息
    * @param string $Type   日志类型 错误 |提醒 |警告|SQL
    */
   static function Set($Message,$Type='NOTICE')
   {   
       if(in_array($Type,C('LOG_TYPE')))
       {
           $Date = date("y-m-d H:i:s");
           self::$Log[] = $Message.'['.$Date.']'."\r\n";
       }
      
   }
   
   /**
    * 储存日志内容到日志文件里
    * @param unknown $MessageType 0=系统配置处理 2=发送邮件处理 3=自定义系统处理
    * @param unknown $Destination 第一项是3的情况下 这里是文件 2的情况下 是邮箱地址
    * @param unknown $ExtraHeaders
    */
   static function Save($MessageType=3,$Destination=null,$ExtraHeaders=null)
   {
       if(!C('LOG_START'))return;
       
       if(is_null($Destination))
       {
           $Destination = LOG_PATH.'/'.date("Y_m_d").'.log';
       }
       
       if($MessageType == 3)
       {
           if(is_file($Destination) && filesize($Destination)>C('LOG_SIZE'))
           {
                rename($Destination,dirname($Destination).'/'.time().'.log');
           }    
       }
       
       error_log(implode(',',self::$Log),$MessageType,$Destination);
   }
   
   /**
    * 直接写入日志文件
    * @param unknown $Message 日志内容信息
    * @param number $MessageType 存储方式
    * @param unknown $Destination 文件地址 或者是邮箱地址
    * @param unknown $ExtraHeaders 头请求 选择储存方式2的时候有用
    */
   static function Write($Message,$MessageType=3,$Destination=null,$ExtraHeaders=null)
   {
       if(!C('LOG_START'))return;
        
       if(is_null($Destination))
       {
           $Destination = LOG_PATH.'/'.date("Y_m_d").'.log';
       }
        
       if($MessageType == 3)
       {
           if(is_file($Destination) && filesize($Destination)>C('LOG_SIZE'))
           {
               rename($Destination,dirname($Destination).'/'.time().'.log');
           }
       }
        
       $Date = date('y-m-d h:i:s');
       $Message = $Message.$Date."\r\n";
       error_log($Message,$MessageType,$Destination);
    }
}