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
 * 框架目录文件操作类
 * @author Vincent
 */
 
Final Class Dir{
    
    /**
     * 转换为标准目录结构
     * @param unknown $DirName 目录路径
     * @return string 标准化处理后的目录路径
     */
    static function DirPath($DirName)
    {
        $DirName = str_ireplace('\\', '/', $DirName);
        return substr($DirName,-1) == '/' ? $DirName : $DirName.'/';
    }
    
    
    /**
     * 获取文件扩展名
     * @param unknown $FileName 文件名称
     * @return String 文件后缀名 不带'.'
     */
    static function GetExt($FileName)
    {
        return substr(strrchr($FileName, '.'), 1);
    }
    
    
    /**
     * 获得目录内容
     * @param unknown $DirName 目录名
     * @param string $Exts 需要获取的文件 的扩展名 (默认所有文件)
     * @param number $Son  是否获得子目录
     * @param array $List  内容列表 (默认是数组)
     * @return array $List 返回内容列表 
     */
    static  function Tree($DirName,$Exts='',$Son=0,$List=array())
    {
        $DirName = self::DirPath($DirName);
        
        if(is_array($Exts))
        {
            $Exts = implode('|',$Exts);
        }
        
        static $Id = 0;
        foreach(glob($DirName."*") as $v )
        {
            $Id++;
            if(!$Exts || preg_match("/\.($Exts)/i",$v))
            {
                $List[$Id]['name']= basename($v);
                $List[$Id]['path']= realpath($v);
                $List[$Id]['type']= filetype($v);
                $List[$Id]['ctime']= filectime($v);
                $List[$Id]['atime']= fileatime($v);
                $List[$Id]['size']= filesize($v);
                $List[$Id]['iswrite']= is_writeable($v);
                $List[$Id]['isread']= is_readable($v);
            }
            
            if(is_dir($v))
            {
                $List = self::Tree($v,$Exts,$Son,$List);
            }
        }
        
        return $List;
    }
    
    
    /**
     * 只获取目录结构
     * @param unknown $DirName 路径
     * @param number $pid 父级
     * @param array $List 
     */
    static function TreeDir($DirName, $Pid=0, $Son=0, $List=array())
    {
        $DirName = self::DirPath($DirName);
        
        static $Id = 0;
        foreach (glob($DirName.'*') as $v)
        {
            if(is_dir($v))
            {
                $Id++;
                $List[$Id]['id']    = $Id;
                $List[$Id]['pid']  = $Pid;
                $List[$Id]['name'] = basename($v);
                $List[$Id]['path'] = realpath($v);
                
                if($Son)
                {
                    $List = self::Tree($v, $Id, $Son, $List);
                }
            }
        }
        
        return $List;
    }
    
    
    /**
     * 删除目录
     * @param unknown $DirName 目录路径
     */
    static function Del($DirName)
    {
        $DirPath = self::DirPath($DirName);
        
        if(!is_dir($DirPath)) return FALSE;
        
        foreach(glob($DirPath.'*') as $v)
        {
            is_dir($v) ? self::Del($v) : unlink($v);
        }
        return rmdir($DirPath);
    }
    
    
    /**
     * 层级目录结构创建
     * @param unknown $DirName 创建目录路径
     * @param String $Auth 文件权限
     * @return boolean
     */
    static function Create($DirName, $Auth="0777")
    {
        $DirPath = self::DirPath($DirName);
        
        if(is_dir($DirPath)) return true;
        
        $DirArr = explode("/",$DirPath);
        
        $Dir = '';
        foreach($DirArr as $v)
        {
            $Dir .= $v.'/';
            if(is_dir($Dir))continue;
            mkdir($Dir, $Auth);
        }
        
        return is_dir($DirPath);
    }
    
    
    /**
     * 复制目录及内容
     * @param unknown $OldPath 源路径
     * @param unknown $NewPath 新路径
     */
    static function Copy($OldPath, $NewPath)
    {
        $OldDir = self::DirPath($OldPath);
        $NewDir = self::DirPath($NewPath);
        if(!is_dir($OldDir))Error('复制失败:['.$OldDir.'] 目录不存在');
        if(!is_dir($NewDir))self::Create($NewDir);
        
        foreach(glob($OldDir.'*') as $v)
        {
            $ToFile = $NewDir.basename($v);
            if(is_file($ToFile))continue;
            if(is_dir($v))
            {
                self::Copy($v, $ToFile);               
            }else{
                copy($v, $ToFile);
                chmod($ToFile, '0777');
            }
        }
        
        return TRUE;
    }
}