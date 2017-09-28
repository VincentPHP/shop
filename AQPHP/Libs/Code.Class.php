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
 * 验证码类
 * @author Vincent
 */
 
Class Code{
    
    /**
     * 私有资源
     * @var unknown
     */
    private $Image; 
    
    /**
     * 画布宽度
     * @var Integer $Width 
     */
    public $Width;
    
    /**
     * 画布高度
     * @var Integer $Height
     */
    public $Height;
    
    /**
     * 背景颜色
     * @var String $BGColor;
     */
    public $BGColor;
    
    /**
     * 验证码
     * @var String $Code
     */
    public $Code;
    
    
    /**
     * 验证码随机种子
     * @var string $CodeStr
     */
    public $CodeStr;
    
    /**
     * 验证码长度
     * @var Integer $CodeLen
     */
    public $CodeLen;
    
    /**
     * 验证码字体
     * @var String $Font
     */
    public $Font;
    
    /**
     * 字体大小
     * @var unknown
     */
    public $FontSize;
    
    /**
     * 验证码字体颜色
     * @var string
     */
    public $FontColor;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
    	$this->CodeStr  = C("CODE_STR");
        $this->Font     = C("FONT");
		$this->Width    = C("CODE_WIDTH");
		$this->Height   = C("CODE_HEIGHT");
		$this->BGColor  = C("CODE_BG_COLOR");
		$this->CodeLen  = C("CODE_LEN");
		$this->FontSize = C("CODE_FONT_SIZE");
		$this->FontColor= C("CODE_FONT_COLOR");
		
		$this->Create();
    }
    
    
    /**
     * 生成验证码
     */
    private function Create_Code()
    {
    	$Code = '';
        for($i=0;$i<$this->CodeLen;$i++)
        {
            $Code .= $this->CodeStr[mt_rand(0, strlen($this->CodeStr)-1)];
        }

        $this->Code = strtoupper($Code);
		$_SESSION[C("CODE")] = $this->Code;
		
    }
    
    
    /**
     * 获得验证码
     */
    public function GetStr()
    {
    	$this->Create_Code();
        return  $this->Code;
    }
    
    
    /**
     * 建画布
     */
    public function Create()
    {
        $W = $this->Width;
        $H = $this->Height;
        $BGColor = $this->BGColor;
        
        if(!$this->CheckGD()) return FALSE;
        
        $Image = imagecreatetruecolor($W, $H);
                    $BGColor = imagecolorallocate($Image,
                    hexdec(substr($BGColor, 1, 2)), 
                    hexdec(substr($BGColor, 3, 2)),
                    hexdec(substr($BGColor, 5, 2))
                 );
        
        imagefill($Image, 0, 0, $BGColor);
        
        $this->Image = $Image;
        $this->Create_Font();
        $this->Create_Pix();        
    }
    
    
    /**
     * 写入验证码文字
     */
    private function Create_Font()
    {
        $this->Create_Code();
        $Color = $this->FontColor;
        
        $FontColor = imagecolorallocate($this->Image,
        				hexdec(substr($Color, 1, 2)), 
                        hexdec(substr($Color, 3, 2)),
                        hexdec(substr($Color, 5, 2))
                     );
        
        $X = $this->Width/$this->CodeLen;
        
        for($i=0;$i<$this->CodeLen;$i++)
        {
        	if(empty($Color))
        	{
        		$FontColor = imagecolorallocate($this->Image, 
        						mt_rand(50,155), 
        						mt_rand(50,155),
        						mt_rand(50,155)
							 );	
        	}
			
            imagettftext($this->Image,$this->FontSize,
            	mt_rand(-30, 30), $X*$i+mt_rand(3, 6),
            	mt_rand($this->Height/1.2, $this->Height-5),
         		$FontColor, $this->Font,$this->Code[$i]
			);
			
        }
        
        $this->FontColor = $FontColor;
    }
    
    
    /**
     * 画 线
     */
    private function Create_Pix()
    {
       $FontColor = $this->FontColor; 
       
       for($i=0;$i<50;$i++)
       {
           imagesetpixel($this->Image, 
              mt_rand(0, $this->Width),
              mt_rand(0, $this->Height),$FontColor
           );
       } 
       
       for($j=0;$j<2;$j++)
       {
           imagesetthickness($this->Image, mt_rand(1,2));  
           imageline($this->Image, 
              mt_rand(0, $this->Width),mt_rand(0, $this->Height),
              mt_rand(0, $this->Width),mt_rand(0, $this->Height),
              $FontColor
           );
       }
       
    }
    
    /**
     * 显示验证码
     */
    public function GetImage()
    {
        header( 'Content-type:image/png' );		//发送头部
        imagepng($this->Image);
        imagedestroy($this->Image);
        
    }
    
    /**
     * 检测GD库是否开启   imagepng函数是否可用
     */
    private function CheckGD()
    {
        return extension_loaded('gd') && function_exists('imagepng');
    }

}