<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Building;
use app\index\model\College;
use app\index\model\Equipment;
use app\index\model\Room;
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

    public function _initialize(){
        session('user.user_id',1);
        session('user.usergroup_id',3);
        session('user.user_name','wkky');
        $this->queryAuthorization();
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        $this->assign('schoolpart_id',-1);
        $this->school_part = $table;
        $this->prefix = config('database.prefix');
        $this->assign('school_part' , $table);
    }
    //校区
//    public function _initialize(){
//        session('user.user_id',1);
//        session('user.usergroup_id',3);
//        session('user.user_name','wkky');
//
//        $this->queryAuthorization();
//        $schoolpart= new Schoolpart();
//        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
//        $this->assign('schoolpart_id',-1);
//        $this->school_part = $table;
//
//        $this->prefix = config('database.prefix');
//        $this->assign('school_part' , $table);
//    }


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
        $authorization = $user['group_authorization'];
        $authorization = json_decode($authorization,true);
//        $authorization = $user['user_authorization'];
//        $authorization = json_decode($authorization,true);
//        if(!$authorization){
//            $authorization = $user['group_authorization'];
//            $authorization = json_decode($authorization,true);
//        }
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
        $room_id = $this->request->param('room_id');
        if(isset($room_id)){
            $this->assign('room_id',$room_id);
        }
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
    //flags 1 :表示报修设备
    public function repairEquipment()
    {
        $flag = $this->request->param("flags");
        if ($flag == 1) {
            $data=$this->request->param("requestData");
            $requestArray=json_decode($data,true);
            $data=[];
            Db::startTrans();
            try{
            for($i = 0; $i < count($requestArray);$i ++){
                $count=$requestArray[$i]['amount'];
                $building=$requestArray[$i]['building'];
                $college=$requestArray[$i]['college'];
                $room=$requestArray[$i]['room'];
                $school=$requestArray[$i]['school'];
                $thing=$requestArray[$i]['text_description'];
                $text=$requestArray[$i]['reasonText'];
                $resultArray=Db::table(config('database.prefix').'equipment')->where(array("room_id"=>$room,"text_description"=>$thing))->select();
                $tempEquipmentId=[];
                for($j = 0; ($count <= count($resultArray)) && ($j < $count); $j ++){
                    $tempEquipmentId[] = $resultArray[$j]['equipment_id'];
                }
                $data[]=array("data"=>$tempEquipmentId,"reason"=>$text,"amount"=>$count,"text_description"=>$thing
                ,"building_id"=>$building,"college_id"=>$college,"schoolpart_id"=>$school,"room_id"=>$room);
            }
            $groupId=session('user.usergroup_id');
            $responseUserId=Db::table(config('database.prefix').'usergroup')->where(array("usergroup_id" => $groupId)) ->value('user_id');
            $actions= array("action"=>"modify","data"=>$data);

            $tempTextDesc=array("schoolpart_id"=>$requestArray[0]['schoolText'],
                "college_id" =>$requestArray[0]['collegeText'],
                "building_id"=>$requestArray[0]['buildingText'],
                "room_id"=>$requestArray[0]['roomText']
                );
                Db::table(config('database.prefix').'requestrecord')->insert(array("requestrecord_id"=>create_guid(),
                    "request_user" => session('user.user_id'),"response_user"=>$responseUserId,
                    "text_description" => json_encode($tempTextDesc,JSON_UNESCAPED_UNICODE),
                    "action"=> json_encode($actions,JSON_UNESCAPED_UNICODE),
                    "status" => 1,
                    "timer" => date("Y-m-d h:i:s"),
                ));
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return ResponseData::getInstance(0, null, array(), array("result" => "error","oo"=>$e), $this->request->isAjax());
            }
            return ResponseData::getInstance(1, null, array(), array("result" => "ok"), $this->request->isAjax());
        }
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
    //显示个人设备报修，及审核信息
    public function  showPersonRepair(){
        return $this->fetch();
    }
    //审核信息初始化  即个人记录
    // ->where("UNIX_TIMESTAMP(timer)",">=", strtotime(date("Y-m-d h:i:s",strtotime("-5 day"))))
    public function initAuditingInfo(){
        $uerId=session('user.user_id');
        $whereNew = array("request_user"=>$uerId,"status" =>1);
        $table = Db::table(config('database.prefix').'viewrequestcord') ->where($whereNew)->whereOr(array("response_user"=>$uerId))
            ->distinct(true)
            ->page($this->request->param("page"),config("lucasPage"))
            ->select();
        for ($i = 0;$i < count($table) ;$i ++){
            $dataActionJsonArray = json_decode($table[$i]['action'] ,true);
            $dataTextDescriptionJsonObj=json_decode($table[$i]['text_description'],true);
            if($table[$i]['kind'] == 1){
                $table[$i]['shoolName'] = $dataTextDescriptionJsonObj['schoolpart_id'];
                $table[$i]['collegeName'] = $dataTextDescriptionJsonObj['college_id'];
                $table[$i]['buildName'] = $dataTextDescriptionJsonObj['building_id'];
                $table[$i]['roomName'] = $dataTextDescriptionJsonObj['room_id'];
                for ($j = 0 ;$j < count($dataActionJsonArray); $j ++){
                    $dataArray = $dataActionJsonArray['data'];
                    $table[$i]['dataRepair'] = $dataArray;
                }
            }
        }
        $tempTable= Db::table(config('database.prefix').'viewrequestcord') ->where($whereNew)->whereOr(array("response_user"=>$uerId))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($tempTable)),$this->request->isAjax());
    }
    //点击查看后，信息的显示
    public function  showLookInfo(){
        $re=$this->request->param("dd");
        $this->assign("dd",$re);
        return $this->fetch();
    }

    //点击确定按钮后，显示需要报修的设备，及理由
    //flag :1 表示报修设备
    public function  showReason()
    {
        $flag = $this->request->param("flag");
        $requestObj = null;
        if ($flag == 1) {
            $requestStr = $this->request->param("request");
            $requestObj = json_decode($requestStr, true);
            $room = "";
            $scholId = "";
            $collegeId = "";
            $buildingId = "";
            foreach ($requestObj[0] as $key => $obj) {
                if ($key == "roomId") {
                    $room = $obj;
                } else if ($key == "scholId") {
                    $scholId = $obj;
                } else if ($key == "collegeId") {
                    $collegeId = $obj;
                } else if ($key == "buildingId") {
                    $buildingId = $obj;
                }
            }
            $roomText = Db::table(config('database.prefix') . 'room')->where(array("room_id" => $room))->value("room_num");
            $SchoolText = Db::table(config('database.prefix') . 'schoolpart')->where(array("schoolpart_id" => $scholId))->value("text_description");
            $collegeText = Db::table(config('database.prefix') . 'college')->where(array("college_id" => $collegeId))->value("text_description");
            $buildingText = Db::table(config('database.prefix') . 'building')->where(array("building_id" => $buildingId))->value("text_description");
            for ($i = 0; $i < count($requestObj); $i++) {
                $requestObj[$i]['roomDescript'] = $roomText;
                $requestObj[$i]['scholDescript'] = $SchoolText;
                $requestObj[$i]['collegeDescript'] = $collegeText;
                $requestObj[$i]['buildingDescript'] = $buildingText;
            }
        }
        $this->assign("flag", $flag);
        $this->assign("data", $requestObj);
        return $this->fetch();
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


    //确定审核通过
    public function  applyPass(){

        $kind=$this->request->param("kind");

        if($kind == 1){
            $flag = true;
            Db::startTrans();
            try{
                $dataObj=json_decode($this->request->param("data"),true);
                $realData=json_decode($dataObj['action'],true);
                $realData=$realData['data'];
                for ($i = 0 ;$i < count($realData) ;$i ++){
                    $realDataTemp=$realData[$i]['data'];

                    for ($j = 0 ;$j < count($realDataTemp) ; $j ++){
                        $realData=$realDataTemp[$j];
                        $table = Db::query('call compute_one(?)',[$realData]);
                        $table = $table[0];
                        foreach ($table as $key=>$value){
                            $result=$table[$key]['message'] ;
                            if($result == 0){
                                $flag =false;

                            }
                        }
                    }

                }

                $where=array("requestrecord_id" =>$dataObj['requestrecord_id']);
                Db::table(config('database.prefix').'requestrecord') ->where($where)
                    ->update(["status" =>$dataObj['status'],"timer"=>date("Y-m-d h:i:s")]);
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return ResponseData::getInstance (0,null,array(),array("status"=>"error"),$this->request->isAjax());
            }
            if(!$flag){
                Db::rollback();
                return ResponseData::getInstance (0,null,array(),array("status"=>"error"),$this->request->isAjax());
            }
            return ResponseData::getInstance (1,null,array(),array("status"=>"ok"),$this->request->isAjax());
        }
    }
    public function  applyNoPass(){

        $kind=$this->request->param("kind");

        if($kind == 1){
            Db::startTrans();
            try{
                $dataObj=json_decode($this->request->param("data"),true);
                $where=array("requestrecord_id" =>$dataObj['requestrecord_id']);
                Db::table(config('database.prefix').'requestrecord') ->where($where)
                    ->update(array("status" =>$dataObj['status'],"timer"=>date("Y-m-d h:i:s")));
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return ResponseData::getInstance (0,null,array(),array("status"=>"error"),$this->request->isAjax());
            }
            return ResponseData::getInstance (1,null,array(),array("status"=>"ok"),$this->request->isAjax());
        }
    }
    //加载历史纪录
    public function historyRecord(){
        $uerId=session('user.user_id');
//        $whereNew = array("request_user"=>$uerId ,'status'=>'!= 1');
        $whereNew = "(status != 1 and request_user = '".$uerId."') or (status != 1 and request_user ='".$uerId."')" ;
        $table = Db::table(config('database.prefix').'viewrequestcord') ->where($whereNew)
            ->distinct(true)
            ->page($this->request->param("page"),config("lucasPage"))
            ->select();
        for ($i = 0;$i < count($table) ;$i ++){
            $dataActionJsonArray = json_decode($table[$i]['action'] ,true);
            $dataTextDescriptionJsonObj=json_decode($table[$i]['text_description'],true);
            if($table[$i]['kind'] == 1){
                $table[$i]['shoolName'] = $dataTextDescriptionJsonObj['schoolpart_id'];
                $table[$i]['collegeName'] = $dataTextDescriptionJsonObj['college_id'];
                $table[$i]['buildName'] = $dataTextDescriptionJsonObj['building_id'];
                $table[$i]['roomName'] = $dataTextDescriptionJsonObj['room_id'];
                for ($j = 0 ;$j < count($dataActionJsonArray); $j ++){
                    $dataArray = $dataActionJsonArray['data'];
                    $table[$i]['dataRepair'] = $dataArray;
                }
            }
        }
        $tempTable= Db::table(config('database.prefix').'viewrequestcord') ->where($whereNew)
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($tempTable)),$this->request->isAjax());
    }
}
