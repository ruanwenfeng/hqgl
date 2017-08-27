<?php
namespace app\index\controller;
use app\extend\ResponseData;
use app\index\model\User;
use think\captcha\Captcha;
use think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/27
 * Time: 21:09
 */
class Base extends Controller{

    public function index(){
        return $this->login();
    }
    public function login(){
        if(session('user'))
            $this->redirect('/index/index');
        return $this->fetch('login');
    }

    public function logout(){
        session(null);
        $this->redirect('/base/login');
    }
    public function checklogin(){

        if(!captcha_check($this->request->param('captcha'))){
            return ResponseData::getInstance (0,'验证码错误',array(),null,$this->request->isAjax());
        };
        $user = new User();
        $user = $user->where(array(
            'user_name'=>$this->request->param('username'),
            'pass'=>$this->request->param('password')
        ))->find();
        if($user){
            session('user',$user);
            return ResponseData::getInstance (1,null,array(),null,$this->request->isAjax());
        }
        return ResponseData::getInstance (0,'用户名或密码错误',array(),null,$this->request->isAjax());
    }
    public function captcha(){
        ob_clean();
        $captcha = new Captcha();
        return $captcha->entry();
    }
}