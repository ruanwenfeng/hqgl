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
use think\console\input\Option;
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
    public $admin;

    public $beforeActionList = ['check'];

    public function check(){
        if(!session('user'))
            $this->redirect('/base/login');
    }

    public function _initialize(){

        $this->queryAuthorization();
        $schoolpart= new Schoolpart();
        $table = $schoolpart->where($this->filterSchoolpart)->order('schoolpart_id')->select();
        $this->assign('schoolpart_id',-1);
        $this->school_part = $table;
        $this->prefix = config('database.prefix');
        $this->assign('school_part' , $table);
        $this->assign('user' , session('user'));
        $option  = Db::table($this->prefix.'options')->where(array('key'=>'year'))->field('value')->select()[0];
        $option = json_decode($option['value'],true);
        $this->assign('power_year' , $option);
    }

    public function checkAdmin(){
        if(!$this->admin){
            session(null);
            if($this->request->isAjax()){
                return ResponseData::getInstance (0,'非法操作',array(),array(),$this->request->isAjax());
            }else{
                $this->redirect('/base/login');
                exit();
            }
        }
        return null;
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
        $user = Viewuser::get(array('user_id'=>session('user.user_id')));
        $authorization = $user['group_authorization'];
        $authorization = json_decode($authorization,true);
//        $authorization = $user['user_authorization'];
//        $authorization = json_decode($authorization,true);
//        if(!$authorization){
//            $authorization = $user['group_authorization'];
//            $authorization = json_decode($authorization,true);
//        }
        $this->filterSchoolpart = array('schoolpart_id'=>['in',$authorization['schoolpart']['id']]);
        $this->admin = true;
        if(false == $authorization['schoolpart']['full']){
            $this->admin = false;
            $this->filterCollege = array('college_id'=>['in',$authorization['college']['id']]);
        }
        else
            $this->filterCollege = null;
        $this->assign('admin', $this->admin);
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
        $this->assign('curr_year',cookie('curr_year'));
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
        $this->assign('curr_year',cookie('curr_year'));
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
        $this->assign('curr_year',cookie('curr_year'));
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
        $this->assign('curr_year',cookie('curr_year'));
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
        for ($i = 0 ; $i < count($table) ; $i ++){
            if($table[$i]["status"] == 2){
                unset($table[$i]);
            }
        }
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
        $year = $this->request->param('year')?$this->request->param('year'):date('Y');
        $table = Db::table(config('database.prefix').'room_power_'.$year)
            ->where(array('room_id'=>$this->request->param('room_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 获取楼栋用电信息
     */
    public function ViewBuildingPower(){
        $year = $this->request->param('year')?$this->request->param('year'):date('Y');
        $table = Db::table(config('database.prefix').'building_power_'.$year)
            ->where(array('building_id'=>$this->request->param('building_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());

    }

    /**
     * 获取学院用电信息
     */
    public function ViewCollegePower(){
        $year = $this->request->param('year')?$this->request->param('year'):date('Y');
        $table = Db::table(config('database.prefix').'college_power_'.$year)
            ->where(array('college_id'=>$this->request->param('college_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     *  获取校区用电信息
     */
    public function ViewSchoolPartPower(){
        $year = $this->request->param('year')?$this->request->param('year'):date('Y');
        $table = Db::table(config('database.prefix').'schoolpart_power_'.$year)
            ->where(array('schoolpart_id'=>$this->request->param('schoolpart_id')))
            ->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }
    //显示个人设备报修，及审核信息
    public function  showPersonRepair(){
        if($this->admin == true){
            $this->assign("isAdmin","shi");
        }else{
            $this->assign("isAdmin","bushi");
        }
        return $this->fetch();
    }
    //审核信息初始化  即个人记录
    // ->where("UNIX_TIMESTAMP(timer)",">=", strtotime(date("Y-m-d h:i:s",strtotime("-5 day"))))
    public function initAuditingInfo(){
        $uerId=session('user.user_id');
        $whereNew = "(status = 1 and request_user = '".$uerId."') or (status = 1 and response_user ='".$uerId."')" ;
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
        $this->checkAdmin();
        $usergroup = new  Usergroup();
        $table = $usergroup->where(array('user_id'=>session('user.user_id')))->select();
        $this->assign('usergroup',$table);
        return $this->fetch('permission');
    }

    public function ShowUserGroup(){
        $this->checkAdmin();
        return $this->fetch('usergroup');
    }

    public function queryUserGroup(){
        $this->checkAdmin();
        $table = Db::table(config('database.prefix').'usergroup')->where(array('user_id'=>session('user.user_id')))->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /*
     * 获取二级账号信息
     */
    public function queryChildUser(){
        $this->checkAdmin();
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
        $this->checkAdmin();
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
        $this->checkAdmin();
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
        $this->checkAdmin();
        sleep(1);
        try{
            $res = Db::table(config('database.prefix').'usergroup')
                ->insert(array(
                    'text_description'=>$this->request->param('text_description'),
                    'usergroup_id'=>create_guid(),
                    'user_id'=>session('user.user_id'),
                    'authorization'=>'{"schoolpart":{"action":[],"id":[],"full":"false"},"college":{"action":[],"id":[]}}'
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
        $this->checkAdmin();
        sleep(1);
        $input = $this->request->param();
        $usergroup_id = $input['usergroup_id'];
        $text_description = null;
        if(array_key_exists('text_description',$input))
            $text_description = $input['text_description'];
        unset($input['usergroup_id']);
        unset($input['/index/saveAuthorization']);
        $authorization = ['schoolpart'=>[
            'action'=>[],'id'=>[],'full'=>false
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
            $error = '保存失败';
            getError($e->getCode(),$error);
            return ResponseData::getInstance (0,$error,array(),array(),$this->request->isAjax());
        }
    }


    //计算用电量，并保存到表里面
    public function savePower(){
        Db::startTrans();
        $year = Db::table($this->prefix.'main')->field('year')->group('year')->select();
        $date = Db::table($this->prefix.'main')->field('date')->group('date')->select();
        $option  = Db::table($this->prefix.'options')->where(array('key'=>'year'))->field('value')->select()[0];
        $option = json_decode($option['value'],true);
        try{
            $data_table = $this->prefix.'main';
            $var = ['room','building','college','schoolpart'];
            foreach ($year as $value){
                if(!in_array((int)intval($value['year']),$option)){
                    $option[] =(int)intval($value['year']);
                }
                foreach ($var as $_var){
                    $table_name = $this->prefix.$_var.'_power_'.$value['year'];
                    $tpl_name = $this->prefix.$_var.'_power_tpl';
                    Db::query('create  table if not exists `'.$table_name.'` like `'.$tpl_name.'`');
                    Db::query('insert into `'.$table_name.'` '.
                        'select main_id, '.$_var.'_id,year,month,date, SUM(num) AS num  from '.$data_table.' '.
                        'where  year = '.$value['year'].' '.
                        'group by '.$_var.'_id,date');
                }
            }
            $now = date('Y-n');
            foreach ($date as $value){
                if($value['date']!=$now){
                    //从data_table 中删除已经插入的
                    Db::table($data_table)->where(array('date'=>$value['date']))->delete();
                    echo  $data_table.'--'.$value['date'].' | ';
                }else{
                    foreach ($year as $_value) {
                        foreach ($var as $_var) {
                            $table_name = $this->prefix.$_var.'_power_'.$_value['year'];
                            Db::table($table_name)->where(array('date'=>$value['date']))->delete();
                        }
                    }
                    //从tabel_name 中删除已经插入的
                }
            }
            Db::table($this->prefix.'options')->where(array('key'=>'year'))->update(array('value'=>json_encode($option)));
        }catch (\Exception $e){
            Db::rollback();
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
        Db::commit();
        return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
    }

    //更改用户所属组
    public function updateUserPermissView(){
        $this->checkAdmin();
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
        $this->checkAdmin();
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
        if(($kind == 1) && ($this->admin == true)){
            $flag = true;
            Db::startTrans();
            try{
                $dataObj=json_decode($this->request->param("data"),true);
                $realDa=json_decode($dataObj['action'],true);
                $realData=$realDa['data'];

                for ($i = 0 ;$i < count($realData) ;$i ++){
                    $realDataTemp=$realData[$i]['data'];
                    for ($j = 0 ;$j < count($realDataTemp) ; $j ++){
                        $realDataconfirm=$realDataTemp[$j];
                        $table = Db::query('call compute_one(?)',[$realDataconfirm]);
                        $table1 = $table[0];
                        foreach ($table1 as $key=>$value){

                            $result=$table1[$key]['message'] ;
                            if($result == 0){
                                var_dump("ddd");
                                $flag =false;
                            }
                        }
                    }
                }
                $where=array("requestrecord_id" =>$dataObj['requestrecord_id']);
                Db::table(config('database.prefix').'requestrecord') ->where($where)
                    ->update(["status" =>$dataObj['status'],"timer"=>date("Y-m-d h:i:s")]);
//                Db::commit();
                if(!$flag){
                    Db::rollback();
                    return ResponseData::getInstance (0,null,array(),array("status"=>"error"),$this->request->isAjax());
                }else{
                    Db::commit();
                }
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return ResponseData::getInstance (0,null,array(),array("status"=>"error"),$this->request->isAjax());
            }
            return ResponseData::getInstance (1,null,array(),array("status"=>"ok"),$this->request->isAjax());
        }
    }
    //审核没有通过
    public function  applyNoPass(){

        $kind=$this->request->param("kind");

        if(($kind == 1) && ($this->admin == true)){
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
    //加载待处理的数据
    public function historyRecord(){
        $uerId=session('user.user_id');

        $whereNew = "(status = 2 and request_user = '".$uerId."') or (status = 2 and response_user ='".$uerId."')" ;

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

    public function createUser(){
        $this->checkAdmin();
        sleep(1);
        try{
            $user = new User();
            $data = $this->request->param(true);
            $res = $user->where(array('user_name'=>$data['user_name']))->select();
            if($res){
                return ResponseData::getInstance (0,'该用户名已经被使用',array(),array(),$this->request->isAjax());
            }else{
                $data['user_id'] = create_guid();
                unset($data['/index/createUser']);
                $n = $user->insert($data);
                if($n){
                    return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
                }else{
                    return ResponseData::getInstance (0,'创建失败',array(),array(),$this->request->isAjax());
                }
            }

        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    public function deleteUser(){
        $this->checkAdmin();
        sleep(1);
        try{
            $user = new User();
            $n = $user->where(array('user_id'=>$this->request->param('user_id')))->delete();
            if($n){
                return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,'删除失败',array(),array(),$this->request->isAjax());
            }
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }
    //修好了重新计算电费
    public function  rePairCulateE(){
        $kind=$this->request->param("kind");
        if(($kind == 1) && ($this->admin == true)){
            Db::startTrans();
            try{
                $dataObj=json_decode($this->request->param("data"),true);

                $realData=json_decode($dataObj['action'],true);
                $realData=$realData['data'];
                for ($i = 0 ;$i < count($realData) ;$i ++){
                    $realDataTemp=$realData[$i]['data'];

                    for ($j = 0 ;$j < count($realDataTemp) ; $j ++){
                        $realDataString=$realDataTemp[$j];

                        Db::table(config('database.prefix').'equipment') ->where(array("equipment_id"=>$realDataString))
                            ->update(["status" =>1,"last_change_date"=>date("Y-m-d h:i:s")]);
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
            return ResponseData::getInstance (1,null,array(),array("status"=>"ok"),$this->request->isAjax());
        }
    }
    //加载历史记录
    public function  realHistory(){
            $uerId=session('user.user_id');
            $whereNew = "((status = 4 or status = 3) and request_user = '".$uerId."') or ((status = 4 or status = 3) and response_user ='".$uerId."')" ;
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
