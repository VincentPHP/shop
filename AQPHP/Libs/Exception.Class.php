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
 * 异常处理类
 * @author Vincent
 */
 
Class Exception extends \Exception{
    
    function __construct($Message,$Code=0){
        
        parent::__construct($Message,$Code);
        
    }
    
    /**
     * 显示错误信息
     * @return string $ErrOr 错误信息
     */
    function Show(){
      
      $Trace = $this->getTrace();
      
      $Error['message'] = 'Message:<strong> '.$this->message .' </strong><br/>';
      $Error['message'].= 'File:'.$this->file.'['.$this->line.'] ';
      $Error['message'].= $Trace[0]['class'].$Trace[0]['type'].$Trace[0]['function'].'()';
      
      array_shift($Trace);
      
      $Info = '';
      foreach ($Trace as $v )
      {
          $Class = isset($v['class'])? $v['class']:'';
          $Type  = isset($v['type']) ? $v['type'] :'';
          $File  = isset($v['file']) ? $v['file'] :'';
          $Info .= $File."\t".$Class.$Type.$v['function'].'<br/>';
      }

      Log::Write($Error['message']);//写入日志
      
      $Error['info']=$Info;
      return $Error;
      
    }

    
}