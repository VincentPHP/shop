<?php
define("CACHE_DIR",'Cache');
define("LOG_DIR",'Log');
define("TPL_DIR",'Tpl');
define("MODULE_DIR",'Application');
define("STATIC_DIR",'Data');
define("PHP_LIBS_DIR",'Libs');
define("PHP_ORG_DIR",'Org');
define("CONFIG_DIR",'Conf');
define("COMMON_DIR",'Common');
define("UPLOAD_DIR",'Upload');
define("CACHE_PATH", TEMP_PATH.'/'.CACHE_DIR);
define("LOG_PATH", TEMP_PATH.'/'.LOG_DIR);
define("TPL_PATH", TEMP_PATH.'/'.TPL_DIR);
define("STATIC_PATH", PHP_PATH.'/'.STATIC_DIR);
define("PHP_ORG_PATH", PHP_PATH.'/'.PHP_ORG_DIR);
define("PHP_LIBS_PATH", PHP_PATH.'/'.PHP_LIBS_DIR);
if(!defined("MODULE_PATH")) define("MODULE_PATH", APP_PATH.'/'. MODULE_DIR);
define("COMMON_PATH", MODULE_PATH.'/'.COMMON_DIR);
define("CONFIG_PATH", COMMON_PATH.'/'.CONFIG_DIR);
define("UPLOAD_PATH", APP_PATH.'/'.UPLOAD_DIR);
function Error($Error){ if(C("DEBUG")){ if(!is_array($Error)){$Backtrace= debug_backtrace();$E['message']=$Error;$Info=''; foreach($Backtrace as$v){$File= isset($v['file'])?$v['file']:'';$Line= isset($v['line'])?$v['line']:'';$Class= isset($v['class'])?$v['class']:'';$Type= isset($v['type'])?$v['type']:'';$Function= isset($v['function'])?$v['function']:'';$Info.=$File.'&nbsp;['.$Line.']&nbsp;'.$Class.$Type.$Function.'()<br/>';}$E['info']=$Info;}else{$E=$Error;}}else{$E['message']= C("ERROR_MESSAGE");} include C('DEBUG_TPL'); exit();}
function object_array($array){ if(is_object($array)){$array=(array)$array;} if(is_array($array)){ foreach($array as$key=>$value){$array[$key]= object_array($value);}} return$array;}
function Notice($Error){ if( C("DEBUG")&& C("NOTICE_SHOW")){$Time= number_format((microtime(true)-\AQPHP\Libs\DeBug::$runTime['App_Start']),4);$Memory= memory_get_usage();$Message=$Error[1];$File=$Error[2];$Line=$Error[3];$Msg="<h1 style='font-size:13px;background-color:#333; height:20px;width:896px;line-height:1.8em; padding:3px;margin-top:10px;color:#fff;'> NOTICE:$Message</h1><div><table style='border:1px solid#dcdcdc;width:902px;padding:5px;'><tr><td>Time</td><td>File</td><td>Line</td></tr><tr><td>$Time</td><td>$Memory</td><td>$File</td><td>$Line</td></tr></table></div>"; echo$Msg;}}
function A($Control){ if(strstr($Control,'.')){$Arr= explode('.',$Control);$Module=$Arr[0];$Control=$Arr[1];}else{$Module= MODULE;} static$_Control= array();$Control=$Control.C("CONTROL_FIX"); if(isset($_Control[$Control])){ return$_Control[$Control];}$ControlPath= MODULE_PATH.'/'.$Module.'/Controller/'.$Control.C("CLASS_FIX").'.php'; LoadFile($ControlPath);$Control="\\$Module\\Controller\\$Control"; if(class_exists($Control)){$_Control[$Control]= new$Control(); return$_Control[$Control];} else{ return false;}}
function M($Table){ return new AQPHP\Libs\Data($Table);}
function I($Method,$Default='',$Anquan=''){ if(empty($Method)){ Error('请传入需要获取的数据方式例如：POST，GET，SESSION');}$Arr= strstr($Method,'.')? explode('.',$Method): Error("请在需要获取方式后面加上'.'");$Arr[1]= strtolower($Arr[1]);	switch(strtoupper($Arr[0])){	case'POST':	if(!empty($Arr[1])){$Data=!empty($_POST[$Arr[1]])?$_POST[$Arr[1]]:$Default;}	else{$Data=$_POST;}	return$Data;	break;	case'GET':	if(!empty($Arr[1])){$Data=!empty($_GET[$Arr[1]])?$_GET[$Arr[1]]:$Default;}	else{$Data=$_GET;}	return$Data;	break;	case'SESSION':	if(!empty($Arr[1])){$Data=!empty($_SERVER[$Arr[1]])?$_SESSION[$Arr[1]]:$Default;}	else{$Data=$_SESSION;}	return$Data;	break;}}
function _Md5($Var){ return md5(serialize($Var));}
function O($Class,$Method=null,$Args=array()){ static$Result= array();$Name= empty($Args)?$Class.$Method:$Class.$Method._Md5($Args); if(!isset($Result[$Name])){$Obj= new$Class(); if(!is_null($Method)&& method_exists($Obj,$Method)){ if(!empty($Args)){$Result[$Name]= call_user_func_array(array(&$Obj,$Method),array($Args));}else{$Result[$Name]=$Obj->$Method();}}else{$Result[$Name]=$Obj;}} return$Result[$Name];}
function LoadFile($file=''){ static$fileArr= array(); if(empty($file)){ return$fileArr;}$filePath= realpath($file); if(isset($fileArr[$filePath])){ return$fileArr[$filePath];} if(!is_file($filePath)){ Error('文件'.$file.'不存在');} require$filePath;$fileArr[$filePath]= true;}
function C($name,$value=null){ static$config= array(); if(is_null($name)){ return$config;} if(is_string($name)){$name= strtolower($name); if(!strstr($name,'.')){ if(is_null($value)){ return isset($config[$name])?$config[$name]: null;} else{$config[$name]=$value; return;}$name= explode(".",$name); if(is_null($value)){ return isset($config[$name[0][1]])?$config[$name[0][1]]: null;} else{$config[$name[0][1]]=$value; return;}}} if(is_array($name)){$config= array_merge($config,array_change_key_case($name)); return true;}}
function DelSpace($FileName){$Data= file_get_contents($FileName);$Data= substr($Data,0,5)=='<?php'? substr($Data,5):$Data;$Data= substr($Data,-2)=='?>'? substr($Data,0,-2):$Data;$PregArr= array('/\/\*.*?\*\/\s*/is','/\/\/.*?[\r\n]/is','/(?!\w)\s*?(?!\w)/is'); return preg_replace($PregArr,'',$Data);}
function P($Msg){ echo'<pre>'; print_r($Msg); echo'</pre>';}
function U($Url){$Module= MODULE;$Control= CONTROL;	if(strstr($Url,'/')){$UrlData= explode('/',$Url);	switch(count($UrlData)){	case 1:$Data="/?m={$Module}&c={$Control}&a={$UrlData[0]}";	break;	case 2:$Data="/?m={$Module}&c={$UrlData[0]}&a={$UrlData[1]}";	break;	default:$Data="/?m={$UrlData[0]}&c={$UrlData[1]}&a={$UrlData[2]}";	break;}}	else{$Data="/?m={$Module}&c={$Control}&a={$Url}";}	return$Data;}
function Session($String,$Value='Return'){ if(!isset($String)){ Error('请输入需要设置或需要获取的SESSION名');} if($Value=='Return'){$Info= isset($_SESSION[$String])&&!is_null($_SESSION[$String])?$_SESSION[$String]: FALSE;} else if($Value==''){ unset($_SESSION[$String]); session_destroy();$Info= FALSE;} else{$_SESSION[$String]=$Value;$Info= TRUE;} return$Info;}
define('IS_POST',($_SERVER['REQUEST_METHOD']=='POST')? TRUE: FALSE);
define('IS_GET',($_SERVER['REQUEST_METHOD']=='GET')? TRUE: FALSE);
define('IS_WIN', strstr(PHP_OS,'WIN')? TRUE: FALSE);
define('IS_CGI', substr(PHP_SAPI, 0, 3)=='cgi'? TRUE: FALSE);
define('IS_CLI', PHP_SAPI=='cli'? TRUE: FALSE);
define('IS_DELETE',$_SERVER['REQUEST_METHOD']=='DELETE'? isset($_POST['_method'])&&$_POST['_method']=='DELETE': FALSE);
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH'])&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest');
define('IS_HTTPS',isset($_SERVER['HTTPS'])&& strtolower($_SERVER['HTTPS'])=='no');
define('IS_WECHAT', strpos($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')!== FALSE);
define('DS', DIRECTORY_SEPARATOR);
define('NOW_MICROTIME', microtime(TRUE));
define('NOW',$_SERVER['REQUEST_TIME']);
define('__ROOT__', trim('http:/'.'/'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']),'/\\'));
define('__URL__', trim('http:/'.'/'.$_SERVER['HTTP_HOST'].'/'.trim($_SERVER['REQUEST_URI'],'/\\'),'/'));
define('__HISTORY__', isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'');
define('VERSION', C('VERSION'));//框架版本号
namespace AQPHP\Libs\Bin;
use AQPHP\Libs\DeBug;
use AQPHP\Libs\Log;
use AQPHP\Libs\Url;
Class App{ static function run(){ spl_autoload_register(array(__CLASS__,"AutoLoad")); set_error_handler(array(__CLASS__,"Error")); set_exception_handler(array(__CLASS__,"Exception")); define("MAGIC_QUOTES_GPC", get_magic_quotes_gpc()? true: false); if(function_exists('date_default_timezone_set')){ date_default_timezone_set(C("DATE_TIMEZONE_SET"));} session_id()|| session_start(); self::Config(); if(C("DEBUG")){ DeBug::Start("App_Start");} self::Init(); if(C("DEBUG")){ DeBug::Show("App_Start","App_End");} Log::Save();} static function Init(){ Url::ParseUrl();$Control= A(MODULE.'.'.CONTROL);$Action= ACTION; if(!method_exists($Control,$Action)){ Error('['.CONTROL.C('CONTROL_FIX')."]控制器中的[{$Action}]动作不存在");}$Control->$Action();} static function Config(){$config_file= CONFIG_PATH.'/Config.php'; if(is_file($config_file)){ C(require$config_file);}} static function Exception($Exception){ Error($Exception);} static function Error($ErrNo,$ErrStr,$ErrFile,$ErrLine){ switch($ErrNo){ case E_ERROR: case E_USER_ERROR:$ErrMsg="ERROR:[{$ErrNo}]<strong>{$ErrStr}</strong><br/>File:{$ErrFile}[{$ErrLine}]"; Log::Write("ERROR:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}[{$ErrLine}]"); Error($ErrMsg); break; case E_NOTICE: case E_WARNING: default:$ErrMsg="NOTICE:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}"."[{$ErrLine}]"; Log::Set("NOTICE:[{$ErrNo}][{$ErrStr}] File:{$ErrFile}[{$ErrLine}]","NOTICE"); Notice(func_get_args()); break;}} static function AutoLoad($ClassName){$StrArr= array('Libs','Common\Controller'); for($i=0;$i<count($StrArr);$i++){ if(strstr($ClassName,$StrArr[$i])){$ClassFile=$ClassName.C('CLASS_FIX').'.php'; if(strstr($ClassName,$StrArr[1])){$ClassFile= MODULE_PATH.'/'.$ClassFile;} if(file_exists($ClassFile)) LoadFile($ClassFile);} else{$ClassFile=$ClassName.'.php'; if(file_exists($ClassFile)) LoadFile($ClassFile);}}}}C(require PHP_PATH.'/Libs/Etc/Init.Config.php'); ?>