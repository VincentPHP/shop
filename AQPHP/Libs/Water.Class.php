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
 * 图标添加水印类
 * @author Vincent
 */

Class Water{
    
    /**
     * 打开图像资源
     * @param String $FilePath 图片路径
     * @return Class $Function 图像资源
     */
    private function OpenImage($FilePath)
    {
        $Info = getimagesize($FilePath);
        
        $Image = ltrim(strchr($Info['mime'], '/'), '/');
        
        $Function = 'imagecreatefrom'.$Image;
        
        $Function = $Function($FilePath);
        
        return $Function;
        
    }
   
    
    /**
     * 根据水印位置获取图片坐标
     * @param Number $ImageW 图片宽度
     * @param Number $ImageH 图片高度
     * @param Number $WaterW 水印宽度
     * @param Number $WaterH 水印高度
     * @param Number $Pct 水印位置
     * @return Number[] 返回X-Y轴坐标
     */
    private function GetWaterPos($ImageW, $ImageH, $WaterW, $WaterH, $Pos=9)
    {
        $PosArr = array();
        switch ($Pos)
        {
            case 1:
                $PosArr[0] = 0;
                $PosArr[1] = 0;
                break;
            case 2:
                $PosArr[0] = ($ImageW-$WaterW)/2;
                $PosArr[1] = 0;
                break;
            case 3:
                $PosArr[0] = $ImageW-$WaterW;
                $PosArr[1] = 0;
                break;
            case 4:
                $PosArr[0] = 0;
                $PosArr[1] = ($ImageH-$WaterH)/2;
                break;
            case 5:
                $PosArr[0] = ($ImageW-$WaterW)/2;
                $PosArr[1] = ($ImageH-$WaterH)/2;
                break;
            case 6:
                $PosArr[0] = $ImageW-$WaterW;
                $PosArr[1] = ($ImageH-$WaterH)/2;
                break;
            case 7:
                $PosArr[0] = 0;
                $PosArr[1] = $ImageH-$WaterH;
                break;
            case 8:
                $PosArr[0] = ($ImageW-$WaterW)/2;
                $PosArr[1] = $ImageH-$WaterH;
                break;
            case 9:
                $PosArr[0] = $ImageW-$WaterW;
                $PosArr[1] = $ImageH-$WaterH;
                break;
        }
        return $PosArr;
    }
    
    
    /**
     * 生成水印图片
     * @param unknown $ImageFile 图片路径
     * @param unknown $WaterFile 水印路径
     * @param string $SaveImage  保存路径
     * @param number $Pos 显示位置
     * @param number $pct 透明度
     */
    public function CreateWater($ImageFile, $WaterFile, $SaveImage='./', $Pos=9, $pct=70)
    {
        if(!is_file($ImageFile)) die($ImageFile.'图片文件不存在！');
        if(!is_file($WaterFile)) die($WaterFile.'水印文件不存在！');
        
        $Image = getimagesize($ImageFile); //获取图片信息
        $Water = getimagesize($WaterFile); //获取水印信息
        
        $ImageFile = $this->OpenImage($ImageFile);//打开图像资源
        $WaterFile = $this->OpenImage($WaterFile);//打开图像资源
        
        $PosArr = $this->GetWaterPos($Image[0], $Image[1], $Water[0], $Water[1], $Pos);

        imagecopymerge($ImageFile, $WaterFile, $PosArr[0], $PosArr[1], 0, 0, $Water[0], $Water[1], $pct);
       
        imagejpeg($ImageFile, $SaveImage);
        
        return TRUE;
    }
}