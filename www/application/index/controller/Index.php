<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Schoolpart;
use app\index\model\Viewcollege;
use app\index\model\Viewuser;
use think\Controller;
class Index extends Controller
{
    public  $filterSchoolpart;
    public  $filterCollege;

    public function _initialize(){
        session('user.user_id',1);
        $this->queryAuthorization();
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        $this->assign('school_part',$table);
    }


    public function index(){
        return $this->fetch();
    }

    public function data(){
        $result = new ResponseData();
        $table = array();
        $table[] = array(
            'name'=>'张三',
            'user_pass'=>'wozai123',
            'user_name'=>'wkky',
            'age'=>18,
        );
        $table[] = array(
            'name'=>'李四',
            'user_pass'=>'wozai123',
            'user_name'=>'wkky',
            'age'=>28,
        );
        $result->data[] = $table;
        $table = array();
        $table[] = array(
            'id'=>'1001',
            'grade'=>'三年级',
            'count'=>180,
        );
        $table[] = array(
            'id'=>'1002',
            'grade'=>'四年级',
            'count'=>500,
        );
        $result->data[] = $table;
        $result->data[] = array();
        return $result->getJSON($this->request->isAjax());
    }

    public function queryAuthorization(){
        $user = Viewuser::get(session('user.user_id'));
        $authorization = $user['user_authorization'];
        $authorization = json_decode($authorization,true);
        if(!$authorization){
            $authorization = $user['group_authorization'];
            $authorization = json_decode($authorization,true);
        }
        $this->filterSchoolpart = array('schoolpart_id'=>['in',$authorization['schoolpart']['id']]);
        if(!$authorization['schoolpart']['full'])
            $this->filterCollege = array('college_id'=>['in',$authorization['college']['id']]);
        else
            $this->filterCollege = null;
    }

    public function getAllSchoolPart(){
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    //显示学院或部门
    public function showCollege(){
        $this->assign('schoolpart_id',$this->request->param('schoolpart_id'));
        return $this->fetch('college');
    }
    public function queryCollege(){
        $where = array();
        $this->filterCollege && $where['college_id'] = $this->filterCollege['college_id'];
        $where['schoolpart_id'] = $this->request->param('schoolpart_id');
        $table = (new Viewcollege())->where($where)->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }
}
