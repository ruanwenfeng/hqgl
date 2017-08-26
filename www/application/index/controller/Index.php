<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Equipment;
use app\index\model\Schoolpart;
use app\index\model\User;
use app\index\model\Usergroup;
use app\index\model\Viewbuilding;
use app\index\model\Viewcollege;
use app\index\model\Viewequipment;
use app\index\model\Viewroom;
use app\index\model\Viewuser;
use think\Controller;
use think\Db;
use think\exception\PDOException;

class Index extends Controller
{
    //1代表成功 0 代表失败
    public  $filterSchoolpart;
    public  $filterCollege;
    public  $school_part;
    public $prefix ;

    //校区
    public function _initialize(){
        session('user.user_id',1);
        session('user.usergroup_id',3);
        $this->queryAuthorization();
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        $this->assign('schoolpart_id',-1);
        $this->school_part = $table;
        $this->prefix = config('database.prefix');
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

    /**
     *  权限过滤
     */
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

    /**
     * 获取所有的校区
     * @return ResponseData|string
     */
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

    /*
     * 显示 楼栋
     */
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

    /**
     * 显示房间
     */
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

    /**
     * 显示用电设备
     */
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

    /**
     * 查询用电设备信息
     */
    public function queryEquipMent(){
        $table = (new Viewequipment())->where([
            'schoolpart_id'=>$this->request->param('schoolpart_id'),
            'college_id'=>$this->request->param('college_id'),
            'building_id'=>$this->request->param('building_id'),
            'room_id'=>$this->request->param('room_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询房间信息
     */
    public function queryRoom(){
        $table = (new Viewroom())->where([
            'schoolpart_id'=>$this->request->param('schoolpart_id'),
            'college_id'=>$this->request->param('college_id'),
            'building_id'=>$this->request->param('building_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询楼栋信息
     */
    public function queryBuilding(){
        $table = (new Viewbuilding())->where([
            'schoolpart_id' => $this->request->param('schoolpart_id'),
            'college_id' => $this->request->param('college_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询学院信息
     */
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
    /**
     *  获取房间用电信息
     */
    public function ViewRoomPower(){
        $table = Db::table(config('database.prefix').'view_room_power')
            ->where(array('room_id'=>$this->request->param('room_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 获取楼栋用电信息
     */
    public function ViewBuildingPower(){
        $table = Db::table(config('database.prefix').'view_building_power')
            ->where(array('building_id'=>$this->request->param('building_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }

    /**
     * 获取学院用电信息
     */
    public function ViewCollegePower(){
        $table = Db::table(config('database.prefix').'view_college_power')
            ->where(array('college_id'=>$this->request->param('college_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }

    /**
     *  获取校区用电信息
     */
    public function ViewSchoolPartPower(){
        $table = Db::table(config('database.prefix').'view_schoolpart_power')
            ->where(array('schoolpart_id'=>$this->request->param('schoolpart_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /*
     * 显示 一级账号下面的二级账号
     */
    public function ShowChildUser(){
        return $this->fetch('permission');
    }

    public function ShowUserGroup(){
        return $this->fetch('usergroup');
    }

    public function queryUserGroup(){
        $table = Db::table(config('database.prefix').'usergroup')->where(array('user_id'=>session('user.user_id')))->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /*
     * 获取二级账号信息
     */
    public function queryChildUser(){
        $table = Db::query('call queryChildUser(?)',[session('user.user_id')]);
        if($table){
            $table = $table[0];
            foreach ($table as $key=>$value){
                $table[$key]['authorization'] = $this->getAuthDeatil($value);
            }
        }
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    public function getAuthDeatil($value){
        $tmp = [];
        $authorization = json_decode( $value['authorization'],true);
        $college = $authorization['college']['id'];
        $_table = Db::table(config('database.prefix').'viewcollege')->where(array('college_id'=>array('in',$college)))->select();

        return group_array('schoolpart_id',$_table,function (&$arr,$value,$key){
            $arr[$value[$key]]['college'] = array();
            $arr[$value[$key]]['title']= $value['校区名称'];
        },function (&$arr,$value,$key){
            $value['flag'] = 'true';
            $arr[$value[$key]]['college'][$value['college_id']] = $value;
        });
    }
    public function authorizationView(){
        $this->assign('usergroup_id',$this->request->param('usergroup_id'));
        $curr_usergroup = Db::table(config('database.prefix').'usergroup usergroup')
            ->where(array('usergroup.usergroup_id'=>$this->request->param('usergroup_id')))
            ->select();
        if(count($curr_usergroup)>0){
            $authorization = $this->getAuthDeatil($curr_usergroup[0]);
            $full_authorization = Db::table(config('database.prefix').'usergroup usergroup')
                ->where(array('usergroup.usergroup_id'=>session('user.usergroup_id')))
                ->select();

            $full_schoolpart = json_decode($full_authorization[0]['authorization'],true)['schoolpart']['id'];


            $full_authorization = Db::table(config('database.prefix').'viewcollege college')
                ->where(array('college.schoolpart_id'=>array('in',$full_schoolpart)))
                ->select();

            $full_authorization = group_array('schoolpart_id',$full_authorization,function (&$arr,$value,$key){
                $arr[$value[$key]]['college'] = array();
                $arr[$value[$key]]['title']= $value['校区名称'];
            },function (&$arr,$value,$key){
                $value['flag'] = 'false';
                $arr[$value[$key]]['college'][$value['college_id']] = $value;
            });
            foreach ($full_authorization as $key =>$value){
                if(in_array($key,array_keys($authorization))){
                    foreach ($value['college'] as $_key=> $_value){
                        if(in_array($_key,array_keys($authorization[$key]['college']))){
                            $full_authorization[$key]['college'][$_key]['flag'] = 'true';
                        }
                    }
                }
            }
            $this->assign('authorization',$full_authorization);
            $this->assign('text_description',$curr_usergroup[0]['text_description']);
            return $this->fetch('authorization');
        }else{
            exit('暂无数据');
        }
    }


    public function deleteUserGroup(){
        sleep(1);
        try{
            $user = new Viewuser();
            $res = $user->where(array('usergroup_id'=>$this->request->param('usergroup_id')))->count();
            if($res>=1){
                return ResponseData::getInstance (0,'删除失败，请先删除该用户组下面的所有用户！',array(),array(),$this->request->isAjax());
            }
            $res = Db::table(config('database.prefix').'usergroup')
                ->where(array(
                    'usergroup_id'=>$this->request->param('usergroup_id')
                ))->delete();
            if($res>=1){
                return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,'删除失败',array(),array(),$this->request->isAjax());
            }
        }catch (\Exception $e){
            return ResponseData::getInstance (0,'删除失败',array(),array(),$this->request->isAjax());
        }
    }

    //创建用户组
    public function createUserGroup(){
        sleep(1);
        try{
            $res = Db::table(config('database.prefix').'usergroup')
                ->insert(array(
                    'text_description'=>$this->request->param('text_description'),
                    'usergroup_id'=>create_guid(),
                    'user_id'=>session('user.user_id'),
                    'authorization'=>'{"schoolpart":{"action":[],"id":[]},"college":{"action":[],"id":[]}}'
                ));
            if($res>=1){
                return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,'添加失败',array(),array(),$this->request->isAjax());
            }
        }catch (PDOException $e){
            $error = '添加失败';
            getError($e->getCode(),$error);
            return ResponseData::getInstance (0,$error,array(),array(),$this->request->isAjax());
        }
    }


    //保存 用户组权限
    public function saveAuthorization(){
        sleep(1);
        $input = $this->request->param();
        $usergroup_id = $input['usergroup_id'];
        $text_description = null;
        if(array_key_exists('text_description',$input))
            $text_description = $input['text_description'];
        unset($input['usergroup_id']);
        unset($input['/index/saveAuthorization']);
        $authorization = ['schoolpart'=>[
            'action'=>[],'id'=>[]
        ],'college'=>[
            'action'=>[],'id'=>[]
        ]];
        $schoolpart = [];
        foreach ($input as $key=>$value){
            if(is_array($value))$schoolpart[$key]=$value;
        }
        foreach ($schoolpart as $key=>$value){
            foreach ($value as $_value){
                if(array_key_exists($_value,$input)){
                    $authorization['schoolpart']['id'][] =$key;
                    $authorization['college']['id'][] =$_value;
                }
            }
        }
        $authorization['schoolpart']['id'] = array_merge(array_unique($authorization['schoolpart']['id']));
        $data = [];
        $text_description && ($data['text_description'] = $text_description);
        $data['authorization'] = json_encode($authorization);
        try{
            $res  = Db::table($this->prefix.'usergroup')->where(array('usergroup_id'=>$usergroup_id))->update($data);
            if($res>=1){
                return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,'保存失败',array(),array(),$this->request->isAjax());
            }
        }catch (PDOException $e){
            $error = '添加失败';
            getError($e->getCode(),$error);
            return ResponseData::getInstance (0,$error,array(),array(),$this->request->isAjax());
        }
    }



    //更改用户所属组
    public function updateUserPermissView(){
        $user_id = $this->request->param('user_id');
        $usergroup_id = $this->request->param('usergroup_id');
        $usergroup = new  Usergroup();
        $table = $usergroup->where(array('user_id'=>session('user.user_id')))->select();
        $this->assign('usergroup',$table);
        $this->assign('usergroup_id',$usergroup_id);
        $this->assign('user_id',$user_id);
        return $this->fetch('updateUserPermiss');
    }

    public function updateUserPermiss(){
        sleep(1);
        $user_id = $this->request->param('user_id');
        $usergroup_id = $this->request->param('usergroup_id');
        $user = new User();
        try{
            $user = $user->find(array('user_id'=>$user_id));
            $usergroup = new  Usergroup();
            $table = $usergroup->where(array('user_id'=>session('user.user_id')))->select();
            $usergroup_ids = [];
            foreach ($table as $value){
                $usergroup_ids[] = $value['usergroup_id'];
            }
            if(in_array($user['usergroup_id'],$usergroup_ids)&&in_array($usergroup_id,$usergroup_ids)){

                $user['usergroup_id'] = $usergroup_id;
                $n = $user->save();
                if($n>=1){
                    return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
                }else{
                    return ResponseData::getInstance (0,'修改失败',array(),array(),$this->request->isAjax());
                }
            }else{
                return ResponseData::getInstance (0,'系统异常',array(),array(),$this->request->isAjax());
            }
        }catch (PDOException $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }
}
