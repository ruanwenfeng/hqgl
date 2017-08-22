<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/20
 * Time: 11:06
 */
namespace app\extend;
class ResponseData
{
    public $status;      //操作是否成功
    public $message;   //错误信息
    public $data;       //数据信息,一般作为表
    public $extra;     //额外说明
    public $meta;      //元数据信息
    public $num;       //table 的数量
    public function __construct($status=1,$message=null,$data=array(),$extra=null){
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->extra = $extra;
    }
    public function getJSON($flag){
        $this->createMeta();
        return $flag?$this:json_encode($this);
    }

    public function createMeta(){
        $length = count($this->data);
        if($length==0){
            return;
        }
        $this->num = $length;
        for($i=0;$i<$length;$i++){
            $table = $this->data[$i];
            $this->meta[$i] = $this->getMeta($table);
        }
    }

    public function getMeta($table){
        $meta = array();
        if(count($table)>0){
            $row = $table[0];
            foreach ($row as $key=>$value){
                $meta[] = $key;
            }
        }
        return $meta;
    }

    public static function getInstance($status=1,$message=null,$data=array(),$extra=null,$flag){
        return (new ResponseData($status,$message,$data,$extra))->getJSON($flag);
    }
}