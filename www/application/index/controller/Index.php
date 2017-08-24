<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Equipment;
use app\index\model\Schoolpart;
use app\index\model\Viewbuilding;
use app\index\model\Viewcollege;
use app\index\model\Viewequipment;
use app\index\model\Viewroom;
use app\index\model\Viewuser;
use think\Controller;
use think\Db;
use think\Request;

class Index extends Controller
{
    //1代表成功 0 代表失败
    public  $filterSchoolpart;
    public  $filterCollege;
    public  $school_part;

    //校区
    public function _initialize(){
        session('user.user_id',1);
        $this->queryAuthorization();
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        $this->assign('schoolpart_id',-1);
        $this->school_part = $table;
        $this->assign('school_part' , $table);
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
        $schoolpart_id = $this->request->param('schoolpart_id');
        $this->assign('schoolpart_id',$schoolpart_id);
        $this->assign('schoolpart_text' ,
            (new Schoolpart())->find(array('schoolpart_id'=>$schoolpart_id))['text_description']);
        return $this->fetch('college');
    }

    public function showBuilding(){
        $schoolpart_id = $this->request->param('schoolpart_id');
        $college_id = $this->request->param('college_id');
        $this->assign('schoolpart_id',$schoolpart_id);
        $this->assign('college_id',$college_id);
        $this->assign('schoolpart_text' ,
            (new Schoolpart())->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new Viewcollege())->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
        return $this->fetch('build');
    }

    public function showRoom(){
        $schoolpart_id = $this->request->param('schoolpart_id');
        $college_id = $this->request->param('college_id');
        $building_id = $this->request->param('building_id');
        $this->assign('schoolpart_id',$schoolpart_id);
        $this->assign('college_id',$college_id);
        $this->assign('building_id',$building_id);
        $this->assign('schoolpart_text' ,
            (new Schoolpart())->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new Viewcollege())->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
        $this->assign('building_text' ,
            (new Viewbuilding())->limit(1)->where(array('building_id'=>$building_id))->find()['text_description']);
        return $this->fetch('room');
    }

    public function showEquipMent(){
        $schoolpart_id = $this->request->param('schoolpart_id');
        $college_id = $this->request->param('college_id');
        $building_id = $this->request->param('building_id');
        $room_id = $this->request->param('room_id');
        $this->assign('schoolpart_id',$schoolpart_id);
        $this->assign('college_id',$college_id);
        $this->assign('building_id',$building_id);
        $this->assign('room_id',$room_id);
        $this->assign('schoolpart_text' ,
            (new Schoolpart())->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new Viewcollege())->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
        $this->assign('building_text' ,
            (new Viewbuilding())->limit(1)->where(array('building_id'=>$building_id))->find()['text_description']);
        $this->assign('room_text' ,
            (new Viewroom())->limit(1)->where(array('room_id'=>$room_id))->find()['room_num']);
        return $this->fetch('equipment');
    }

    public function queryEquipMent(){
        $table = (new Viewequipment())->where([
            'schoolpart_id'=>$this->request->param('schoolpart_id'),
            'college_id'=>$this->request->param('college_id'),
            'building_id'=>$this->request->param('building_id'),
            'room_id'=>$this->request->param('room_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    public function queryRoom(){
        $table = (new Viewroom())->where([
            'schoolpart_id'=>$this->request->param('schoolpart_id'),
            'college_id'=>$this->request->param('college_id'),
            'building_id'=>$this->request->param('building_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    public function queryBuilding(){
        $table = (new Viewbuilding())->where([
            'schoolpart_id' => $this->request->param('schoolpart_id'),
            'college_id' => $this->request->param('college_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    public function queryCollege(){
        $where = array();
        $this->filterCollege && $where['college_id'] = $this->filterCollege['college_id'];
        $where['schoolpart_id'] = $this->request->param('schoolpart_id');
        $table = (new Viewcollege())->where($where)->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    //lucas 查询校区
    public function  faultRepair(){
        return $this->fetch();
    }
    public function faultRepairSchool(){
        $schoolTable=$this->school_part;
        return ResponseData::getInstance (1,null,array($schoolTable),array('total'=>count($schoolTable)),$this->request->isAjax());
    }
    public function lucasQueryEquipment(){
        $where["room_id"]=$this->request->param("room_id");
        $table=(new Equipment())->where($where)->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }
    //完成报修请求
    public function repairEquipment()
    {
        return ResponseData::getInstance(1, null, array(), array(), $this->request->isAjax());
    }
    public function ViewRoomPower(){
        $table = Db::table(config('database.prefix').'view_room_power')
            ->where(array('room_id'=>$this->request->param('room_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    public function ViewBuildingPower(){
        $table = Db::table(config('database.prefix').'view_building_power')
            ->where(array('building_id'=>$this->request->param('building_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }

    public function ViewCollegePower(){
        $table = Db::table(config('database.prefix').'view_college_power')
            ->where(array('college_id'=>$this->request->param('college_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }

    public function ViewSchoolPartPower(){
        $table = Db::table(config('database.prefix').'view_schoolpart_power')
            ->where(array('schoolpart_id'=>$this->request->param('schoolpart_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }
}
