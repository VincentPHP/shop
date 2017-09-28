<?php
/* +----------------------------------------------------------------
 * | Software: [AQPHP framework]
 * |	 Site: www.aqphp.com
 * |----------------------------------------------------------------
 * |   Author: 赵 港 < admin@gzibm.com | 847623251@qq.com >
 * |   WeChat: GZIDCW
 * |   Copyright (C) 2015-2020, www.aqphp.com All Rights Reserved.
 * +----------------------------------------------------------------*/
namespace AQPHP\Libs\WeChat;
/**
 * 微信开发类
 * @author Vincent
 */
 
Class WeChat{
    
    private $AppId;
    
    private $ToKen;
    
    private $AppSecret;
    
    //表示QRCODE的类型 
    const QRCODE_TYPE_TEMP  = 1;
    const QRCODE_TYPE_LIMIT = 2;
    const QRCODE_TYPE_LIMIT_STR = 3;
    
    public function __construct($Id,$ToKen,$Secret)
    {
        
        $this->AppId =     $Id;
        $this->ToKen =     $ToKen;
        $this->AppSecret = $Secret;
       
    } 
    
    /**
     * 获取AccessToken
     * @param string $TokenFile 用来存储Token的临时文件
     */
    public function GetAccessToken($TokenFile='./AccessToken')
    {
        $LifeTime =7200;
        var_dump($TokenFile);
        
        if(file_exists($TokenFile) && time()-filemtime($TokenFile)<$LifeTime)
        {
            
            return file_get_contents($TokenFile);
            
        }
        
        $Url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->AppId}&secret={$this->AppSecret}";

        $Result = $this->RequestGet($Url);

        if(!$Result)
        {
            return false;
        }
        
        $ResultObj = json_decode($Result);

        file_put_contents($TokenFile,$ResultObj->access_token);
        
        return $ResultObj->access_token;
    }
    

    /**
     * 
     * @param unknown $Content
     * @param number $Type
     * @param number $Expire
     */
    public function GetQRCode($Content, $File='', $Type=2, $Expire=604800)
    {
        $Tiket = $this->GetQRCodeTiket($Content,$Type=2,$Expire=604800);
        
        $Url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=$Tiket";
        
        $Result = $this->RequestGet($Url);
        
        if($File)
        {
            file_put_contents($File,$Result);
           
        }else{
            
            header("Content-type:image/jpeg");
            echo $Result;
        }
        
    }
    
    /**
     * 
     * @param unknown $Url
     * @param unknown $Data
     * @param string $Ssl
     * @return boolean|unknown
     */
    private function RequestPost($Url,$Data,$Ssl=true)
    {
        
        //curl完成
        $Curl = curl_init();
        
        //设置curl选项
        curl_setopt($Curl,CURLOPT_URL,$Url);
        
        $UserAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0';
        
        curl_setopt($Curl,CURLOPT_USERAGENT,$UserAgent);
        
        curl_setopt($Curl,CURLOPT_AUTOREFERER,true);
        
        if($Ssl)
        {
            curl_setopt($Curl,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($Curl,CURLOPT_SSL_VERIFYHOST,1);
        }
        
        curl_setopt($Curl,CURLOPT_POST,true);
        curl_setopt($Curl,CURLOPT_POSTFIELDS,$Data);
        
        curl_setopt($Curl,CURLOPT_HEADER,false);
        curl_setopt($Curl,CURLOPT_RETURNTRANSFER,true);
        
        $Response = curl_exec($Curl);
        
        if(false === $Response)
        {
            echo '<br/>'.curl_error($Curl).'<br/>';
            return false;
        }
        
        return $Response;
        
    }
    
    /**
     * 
     * @param unknown $Url
     * @param string $Ssl
     * @return boolean|unknown
     */
    private function RequestGet($Url,$Ssl=true)
    {
       //curl完成
        $Curl = curl_init();
        
        //设置curl选项
        curl_setopt($Curl,CURLOPT_URL,$Url);
        
        $UserAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:50.0) Gecko/20100101 Firefox/50.0';
        
        curl_setopt($Curl,CURLOPT_USERAGENT,$UserAgent);
        
        curl_setopt($Curl,CURLOPT_AUTOREFERER,true);
        
        //SSL相关
        if($Ssl)
        {
            curl_setopt($Curl,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($Curl,CURLOPT_SSL_VERIFYHOST,1);
        }

        curl_setopt($Curl,CURLOPT_HEADER,false);
        curl_setopt($Curl,CURLOPT_RETURNTRANSFER,true);
        
        //发出请求
        $Response = curl_exec($Curl);
       
        if(false === $Response)
        {
            echo '<br/>'.curl_error($Curl).'<br/>';
            return false;
        }
        
        return $Response;
    
    
    }
    
    /**
     * 
     * @param unknown $Content
     * @param number $Type
     * @param number $Expire
     * @return boolean
     */
    public function GetQRCodeTicket($Content,$Type=2,$Expire=604800)
    {
        $AccessToken = $this->GetAccessToken();
        
        $Url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$AccessToken";
           
        $TypeList = array(
            self::QRCODE_TYPE_TEMP => 'QR_SCENE',
            self::QRCODE_TYPE_LIMIT => 'QR_LIMIT_SCENE',
            self::QRCODE_TYPE_LIMIT_STR =>'QR_LIMIT_STR_SCENE',
            
        );
        
        $ActionName = $TypeList[$Type];
        
        switch($Type){
           
            case self::QRCODE_TYPE_TEMP:
                $DataArr['expire_seconds'] = $Expire;
                $DataArr['action_name'] = $ActionName;
                $DataArr['action_info']['scene']['scene_id'] = $Content;
            break;
            
            case self::QRCODE_TYPE_LIMIT:
            case self::QRCODE_TYPE_LIMIT_STR:
                $DataArr['action_name'] = $ActionName;
                $DataArr['action_info']['scene']['scene_id'] = $Content;
            break;
                
        }
        
        $Data = json_encode($DataArr);
        
        $Result = $this->RequestPost($Url,$Data);
        
        if(!$Result)
        {
            return false;
        }
        
        //处理响应数据
        $ResultObj = json_decode($Result);
        
        return $ResultObj->ticket;
    }
    
    /**
     * 第一次验证URL合法性
     */
    public function FirstValid()
    {
        if($this->CheckSignature())
        {
            echo $_GET['echostr'];
        }
    }
    
    /**
     * 
     * @return boolean
     */
    private function CheckSignature()
    {   
        //获得微信公众平台请求的验证数据
        $Signature = $_GET['signature'];
        $TimeStamp = $_GET['timestamp'];
        $Nonce     = $_GET['nonce'];
        
        $TmpArr = array($this->ToKen,$TimeStamp,$Nonce);
        
        sort($TmpArr,SORT_STRING); //字典顺序
        
        $TmpStr = implode($TmpArr);//连接
        
        $TmpStr = sha1($TmpStr);
        
        $State = $Signature == $TmpStr ? TRUE : FALSE;

    }
    
    /**
     * 
     */
    public function ResponseMSG()
    {
        
       $XmlStr = $GLOBALS['HTTP_RAW_POST_DATA'];
       
       if(empty($XmlStr))
       {
           die('');
       }
       
       libxml_disable_entity_loader(true);
       
       $RequestXml = simplexml_load_string($XmlStr,'SimpleXMLElement',LIBXML_NOCDATA);
        
       switch($RequestXml->MsgType){
           case 'event':
               
               $Event = $RequestXml->Event;
               
               if('subscribe' == $Event)
               {
                   $this->DoSubScribe($RequestXml);
               }
               
               break;
            default:
               break;
       }
    }
        
    /**
     * 
     * @param unknown $RequestXml
     */
    private function DoSubScribe($RequestXml)
    {
        $Text = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <MsgId>%s</MsgId>
                </xml>';
        
        $Content = '感谢您关注致远';
        
        $Response = sprintf($Text,$RequestXml->FromUserName,$RequestXml->ToUserName,time(),$Content,mt_rand());
        
        die($Response);
    }
    
    
    public function MenuSet($Menu)
    {
        
        $Url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->GetAccessToken();
        
        $Data = $Menu;
        
        $Result = $this->RequestPost($Url,$Data);
        
        if($Result->ErrCode == 0)
        {
            return true;
        }
        else
        {
            echo $Result->ErrMsg.'<br/>';
            return false;
        } 
    }
}