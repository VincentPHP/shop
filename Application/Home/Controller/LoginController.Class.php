<?php
/**
 * Created by PhpStorm.
 * User: admin_isk27uk
 * Date: 2017/9/28
 * Time: 19:32
 */

namespace Home\Controller;      //固定写法 只争对前期
use AQPHP\Libs\Controller;
use AQPHP\Libs\Image;
use AQPHP\Libs\Upload;      //固定写法 只争对前期

class LoginController extends Controller
{
    public function index()
    {
        if(IS_POST)         //IS_POST  判断是否是POST提交  IS_GET
        {
            $data = I('post.'); //I()接受提交的内容 I('get.')  I('post.') 接受全部 I('post.name','0');

            // M(表名)->where('字段','条件=><','查找的数据')->find()只取一条
            $name = M('user')->where('name','=', $data['loginname'])->find();

            if($name)
            {
                //MD5(需要加密的数据)
                $pwd = M('user')->where('password','=', md5($data['nloginpwd']))->find();

                if($pwd)
                {
                    //成功跳转方法  提示内容 跳转地址 几秒跳转
                    $this->Success('恭喜您登陆成功','http://www.qq.com',3);
                    exit();
                }
                else
                {
                    //失败跳转方法  提示内容 跳转地址 几秒跳转
                    $this->Error('用户名或密码错误。。','',6);
                }
            }
            else
            {
                $this->Error('用户名或密码错误。。','',6);
            }
        }

        //分配变量 到页面 (变量名,数据) 前台用{{变量名}}获取数据
        $this->Assign('lcxm','刘闯');

        //显示页面
        $this->Display();
    }

    public function water()
    {

        if(IS_POST)
        {

            //调用上传文件类 回自动返回上传文件位置
            $up = new Upload();
            $file_path = $up->upload();


            //调用图片类 生成一个水印
            $img = new Image();
            $img->watermark($file_path[0]['path'],'','Public/water.jpg');

            $this->Success('图片上传成功,生成了水印和缩略图','','');
            exit();
        }

        $this->Display();
    }

}