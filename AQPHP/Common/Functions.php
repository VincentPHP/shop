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
 * 输出错误信息
 * @param String $msg 错误信息
 */
function Error($Error)
{
    if(C("DEBUG"))
    {
        if(!is_array($Error))
        {  
            $Backtrace = debug_backtrace();

            $E['message'] = $Error;
    
            $Info = '';
            foreach ($Backtrace as $v )
            {
                $File = isset($v['file']) ? $v['file'] :'';
                $Line = isset($v['line']) ? $v['line'] :'';
                $Class= isset($v['class'])? $v['class']:'';
                $Type = isset($v['type']) ? $v['type'] :'';
                $Function = isset($v['function'])?$v['function']:'';
                $Info .= $File.'&nbsp;['.$Line.']&nbsp;'.$Class.$Type.$Function.'()<br/>';
            }
            
            $E['info'] = $Info;   
        
		}else{    
            $E = $Error;
        }
        
    }else{
        
        $E['message'] = C("ERROR_MESSAGE");
    }
    
   include C('DEBUG_TPL');
   exit();
}


/**
 * 对象转数组
 * @param Array $array 对象数组
 * @return Array 数组  
 */
function object_array($array)
{
    if(is_object($array))
    {
        $array = (array)$array;
    }
    
    if(is_array($array))
    {
        foreach($array as $key=>$value)
        {
            $array[$key] = object_array($value);
        }
    }
    
    return $array;
}


/**
 * 提示性错误
 * @param unknown $Msg
 */
function Notice($Error)
{
    if( C("DEBUG") && C("NOTICE_SHOW"))
    {
        $Time = number_format((microtime(true)-\AQPHP\Libs\DeBug::$runTime['App_Start']),4);
        
        $Memory = memory_get_usage();
        
        $Message = $Error[1];
        $File    = $Error[2];
        $Line    = $Error[3];
        
        $Msg = "
        <h1 style='font-size:13px;background-color:#333;
                   height:20px;width:896px;line-height:1.8em;
                   padding:3px;margin-top:10px;color:#fff;'>
            NOTICE:$Message
        </h1>
        <div>
            <table style='border:1px solid #dcdcdc;width:902px;padding:5px;'>
                <tr><td>Time</td><td>File</td><td>Line</td></tr>
                <tr><td>$Time</td><td>$Memory</td><td>$File</td><td>$Line</td></tr>
            </table>    
        </div>";
        echo $Msg;  
    }
}


/**
 * 实例化控制器
 * @param unknown $Control
 */
function A($Control)
{
   if(strstr($Control,'.'))
   { 
       $Arr = explode('.',$Control);
       
       $Module  = $Arr[0];
       $Control = $Arr[1];
       
   }else{
       
       $Module = MODULE;
   }
   
   static $_Control = array();
   
   $Control = $Control.C("CONTROL_FIX");
   
   if(isset($_Control[$Control]))
   {
       return $_Control[$Control];
   }
   
   $ControlPath = MODULE_PATH.'/'.$Module.'/Controller/'.$Control.C("CLASS_FIX").'.php';

   LoadFile($ControlPath);//载入文件函数
   
   $Control = "\\$Module\\Controller\\$Control";
   
   if(class_exists($Control))
   {
       $_Control[$Control] = new $Control();
       return $_Control[$Control];   
   }
   else
   {    
       return false;
   }
}

/**
 * 实例化表
 * @param unknown $Table
 */
function M($Table)
{
    return new AQPHP\Libs\Data($Table);
}


/**
 * 接收处理数据
 * @param unknown $Method 数据提交方式
 * @param string $Default 默认值
 * @param string $Anquan 
 * @return unknown $Data 提交过滤后的数据
 */
function I($Method, $Default='', $Anquan='')
{
    if(empty($Method))
    {
       Error('请传入需要获取的数据方式 例如：POST，GET，SESSION');  
    }
	
	$Arr = strstr($Method,'.') ? explode('.', $Method) : Error("请在需要获取方式后面加上'.'");
	
	$Arr[1] = strtolower($Arr[1]);
	
	switch(strtoupper($Arr[0]))
	{
		case 'POST':
	
			if(!empty($Arr[1]))
        	{
        		$Data = !empty($_POST[$Arr[1]]) ? $_POST[$Arr[1]] : $Default;
			}
			else
			{
				$Data = $_POST;	
			}
			
			return $Data;
			
		break;
		
		case 'GET':
			
			if(!empty($Arr[1]))
        	{
        		$Data = !empty($_GET[$Arr[1]]) ? $_GET[$Arr[1]] : $Default;
			}
			else
			{
				$Data = $_GET;	
			}
			
			return $Data;

		break;
			
		case 'SESSION':
			
			if(!empty($Arr[1]))
        	{
        		$Data = !empty($_SERVER[$Arr[1]]) ? $_SESSION[$Arr[1]] : $Default;
			}
			else
			{
				$Data = $_SESSION;	
			}
			
			return $Data;

		break;	
	}    
}

/**
 * 生成唯一序列号
 * @param String $Var 参数
 */
function _Md5($Var)
{
    return md5(serialize($Var));
}


/**
 * 实例化对象或执行方法
 * @param String $Class  实例化类
 * @param String $Method 方法名
 * @param array $Args    参数
 * @return obj  $Result  对象
 */
function O($Class,$Method=null,$Args=array())
{
    static $Result = array(); 
    
    $Name = empty($Args)? $Class.$Method : $Class.$Method._Md5($Args);
      
    if(!isset($Result[$Name]))
    {
        $Obj = new $Class();
        
        if(!is_null($Method) && method_exists($Obj,$Method))
        {
            if(!empty($Args))
            {
               $Result[$Name] = call_user_func_array(array(&$Obj,$Method),array($Args));
               
            }else{
                
                $Result[$Name] = $Obj->$Method();
            }
            
        }else{
            $Result[$Name] = $Obj;
        }
    }
    
    return $Result[$Name];
}


/**
 * 载入文件
 * @param String $file 需要载入的文件名
 */
function LoadFile($file='')
{
    static $fileArr = array();
        
    if(empty($file))
    {
       return $fileArr;  
    }
    
    $filePath = realpath($file);

    if(isset($fileArr[$filePath]))
    {
       return $fileArr[$filePath];
    }
	    
    if(!is_file($filePath))
    {
       Error('文件'.$file.'不存在');
    }
    
    require $filePath;
    
    $fileArr[$filePath] = true;
   
    //include $fileArr[$filePath];

}


/**
 * 配置文件处理
 * @param String $name 配置项名
 * @param String $value 配置项值
 */
function C($name,$value=null)
{
    static $config = array();
    
    if(is_null($name)) //无name值 则调用全部配置项
    {
        return $config;
    }
    
    if(is_string($name))
    {
       $name = strtolower($name); //转换为小写
        
       if(!strstr($name,'.'))
       {
           if(is_null($value)) //判断是否存在默认值 
           {
               return isset($config[$name]) ? $config[$name] : null ; //无默认值情况下 返回配置项值
           }
           else
           {
               $config[$name] = $value; //有值 则赋值给配置项 
               return; 
           } 
            
           $name = explode(".",$name);
            
           if(is_null($value))
           {
               return isset($config[$name[0][1]])? $config[$name[0][1]] : null ;
           }
           else
           {
               $config[$name[0][1]] = $value ;
               return;
           }
       }
   }
    
   if(is_array($name))
   {
       $config = array_merge($config,array_change_key_case($name));//合并数组 数组键名转换为大写
       return true;
   }
}

/**
 * 格式化内容 去空白
 * @param unknown $FileName 文件名
 */
function DelSpace($FileName)
{
    $Data = file_get_contents($FileName);
    
    $Data = substr($Data,0,5)=='<?php'? substr($Data,5)  : $Data ;//删除php开始标记
    $Data = substr($Data,-2) =='?>' ? substr($Data,0,-2) : $Data ;//删除php结束标记
    
    $PregArr = array('/\/\*.*?\*\/\s*/is','/\/\/.*?[\r\n]/is','/(?!\w)\s*?(?!\w)/is'); 
    return  preg_replace($PregArr,'',$Data);//正则替换
}

/**
 * 格式化打印
 * @param String $Msg 打印的内容
 */
function P($Msg)
{
    echo '<pre>';
    
    print_r($Msg);
    
    echo '</pre>';
}

/**
 * 转跳函数 
 * @param String $Url 模块/控制/方法
 * @return string $Data 转跳网址
 */
function U($Url)
{	
    $Module = MODULE;
    $Control= CONTROL;
    
	if(strstr($Url, '/'))
	{
		$UrlData = explode('/', $Url);
				
		switch(count($UrlData))
		{
			case 1:
				$Data = "/?m={$Module}&c={$Control}&a={$UrlData[0]}";
			break;
				
			case 2:
				$Data = "/?m={$Module}&c={$UrlData[0]}&a={$UrlData[1]}";	
			break;
				
			default:
				$Data = "/?m={$UrlData[0]}&c={$UrlData[1]}&a={$UrlData[2]}";				
			break;			
		}
	}	
	else
	{
		$Data = "/?m={$Module}&c={$Control}&a={$Url}";
	}
	
	return $Data;
}


/**
 * 设置SESSION 或 获取SESSION值
 * @param String $String SESSION名
 * @param unknown $Value SESSION值 
 * @return String $_SESSION  
 */
function Session($String, $Value='Return')
{
    if(!isset($String))
    {
        Error('请输入需要设置或需要获取的SESSION名');
    }
    
    if($Value == 'Return')
    {
        $Info = isset($_SESSION[$String]) && !is_null($_SESSION[$String]) ? $_SESSION[$String] : FALSE; 
    }
    else if($Value == '')
    {
        unset($_SESSION[$String]);
        session_destroy();
        $Info = FALSE;
    }
    else
    {
        $_SESSION[$String] = $Value;
        $Info = TRUE;
    }

    return $Info;
}