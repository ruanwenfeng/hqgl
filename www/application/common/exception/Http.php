<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 13:43
 */
namespace app\common\exception;
use think\exception\Handle;
use Exception;
class Http extends Handle
{
    public function render(Exception $e){
        if(config('app_debug'))
            return parent::render($e);
        else{
            header('Location: '.url('index/_404'));
            return null;
        }
    }
}