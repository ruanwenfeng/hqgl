<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Building;
use app\index\model\College;
use app\index\model\Equipment;
use app\index\model\Record;
use app\index\model\Room;
use app\index\model\Schoolpart;
use app\index\model\User;
use app\index\model\Usergroup;
use app\index\model\Viewcollege;
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
        $this->assign('admin' , $this->admin);
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
            }else if($this->request->header('xhr-type') == 'fetch'){
                return ResponseData::getInstance (0,'非法操作',array(),array(),false);
            }
            else{
                $this->redirect('/base/login');
                exit();
            }
        }
        return ResponseData::getInstance (1,null,array(),array(),false);
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
//        $this->assign('schoolpart_text' ,
//            (new Schoolpart())->find(array('schoolpart_id'=>$schoolpart_id))['text_description']);
        $this->assign('schoolpart_text' ,
            (new Schoolpart())->field('text_description')->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
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
            (new Schoolpart())->field('text_description')->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new College())->field('text_description')->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
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
            (new Schoolpart())->field('text_description')->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new College())->field('text_description')->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
        $this->assign('building_text' ,
            (new Building())->field('text_description')->limit(1)->where(array('building_id'=>$building_id))->find()['text_description']);
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
            (new Schoolpart())->field('text_description')->limit(1)->where(array('schoolpart_id'=>$schoolpart_id))->find()['text_description']);
        $this->assign('college_text' ,
            (new College())->field('text_description')->limit(1)->where(array('college_id'=>$college_id))->find()['text_description']);
        $this->assign('building_text' ,
            (new Building())->field('text_description')->limit(1)->where(array('building_id'=>$building_id))->find()['text_description']);
        $this->assign('room_text' ,
            (new Room())->field('room_num')->limit(1)->where(array('room_id'=>$room_id))->find()['room_num']);
        return $this->fetch('equipment');
    }

    /**
     * 查询用电设备信息
     */
    public function queryEquipMent(){
//        $table = (new Viewequipment())->where([
//            'schoolpart_id'=>$this->request->param('schoolpart_id'),
//            'college_id'=>$this->request->param('college_id'),
//            'building_id'=>$this->request->param('building_id'),
//            'room_id'=>$this->request->param('room_id')])->select();
        $table = Db::query('call viewequipment(?)',[$this->request->param('room_id')])[0];
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询房间信息
     */
    public function queryRoom(){
//        $table = (new Viewroom())->where([
//            'schoolpart_id'=>$this->request->param('schoolpart_id'),
//            'college_id'=>$this->request->param('college_id'),
//            'building_id'=>$this->request->param('building_id')])->select();
        $table = Db::query('call viewroom(?,?,?)',[$this->request->param('schoolpart_id'),
            $this->request->param('college_id'),$this->request->param('building_id')])[0];
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询楼栋信息
     */
    public function queryBuilding(){
        $table = Db::query('call viewbuilding(?,?)',
            [$this->request->param('schoolpart_id'),$this->request->param('college_id')])[0];
//        $table = (new Viewbuilding())->where([
//            'schoolpart_id' => $this->request->param('schoolpart_id'),
//            'college_id' => $this->request->param('college_id')])->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /**
     * 查询学院信息
     */
    public function queryCollege(){
        $where = array();
        $this->filterCollege && $where['college_id'] = $this->filterCollege['college_id'];
        $where['schoolpart_id'] = $this->request->param('schoolpart_id');
        $schoolpart_text = (new Schoolpart())->limit(1)
            ->where(array('schoolpart_id'=> $where['schoolpart_id']))->find()['text_description'];
        $table = (new College())->field('\''.$schoolpart_text.'\' as `校区名称`,college_id,text_description')->where($where)->select();
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        $usergroup = new  Usergroup();
        $table = $usergroup->where(array('user_id'=>session('user.user_id')))->select();
        $this->assign('usergroup',$table);
        return $this->fetch('permission');
    }

    public function ShowUserGroup(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        return $this->fetch('usergroup');
    }

    public function queryUserGroup(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        $table = Db::table(config('database.prefix').'usergroup')->where(array('user_id'=>session('user.user_id')))->select();
        return ResponseData::getInstance (1,null,array($table),array('total'=>count($table)),$this->request->isAjax());
    }

    /*
     * 获取二级账号信息
     */
    public function queryChildUser(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $_table = Db::table(config('database.prefix').'college college')
            ->field('college.schoolpart_id,college_id,schoolpart.text_description `校区名称`,hqgl_college.text_description')
            ->where(array('college_id'=>array('in',$college)))
            ->join(config('database.prefix').'schoolpart schoolpart','college.schoolpart_id = schoolpart.schoolpart_id')
            ->select();
        return group_array('schoolpart_id',$_table,function (&$arr,$value,$key){
            $arr[$value[$key]]['college'] = array();
            $arr[$value[$key]]['title']= $value['校区名称'];
        },function (&$arr,$value,$key){
            $value['flag'] = 'true';
            $arr[$value[$key]]['college'][$value['college_id']] = $value;
        });
    }
    public function authorizationView(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
            $full_authorization = Db::table(config('database.prefix').'college college')
                ->field('college.schoolpart_id,college_id,schoolpart.text_description `校区名称`,hqgl_college.text_description')
                ->where(array('college.schoolpart_id'=>array('in',$full_schoolpart)))
                ->join(config('database.prefix').'schoolpart schoolpart','college.schoolpart_id = schoolpart.schoolpart_id')
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        set_time_limit(0);
        $message = Db::query('call new_compute(?)',[date('Y-m-d')])[0][1]['message'];
        set_time_limit(30);
        if(intval( $message)==1){
            return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
        }else{
            return ResponseData::getInstance (0,'计算失败',array(),array(),$this->request->isAjax());
        }
    }

    //更改用户所属组
    public function updateUserPermissView(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
    //创建用户
    public function createUser(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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
    //删除用户
    public function deleteUser(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
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

    //修改密码
    public function editPassView(){
        return $this->fetch('editpass');
    }
    public function editPass(){
        $user = new User();
        $user = $user->where(array('user_id'=>session('user.user_id')))->find();
        if($user){
            if($user['pass'] == $this->request->param('pass')){
                $user['pass'] = $this->request->param('new_pass');
                if($user->save()>=1){
                    return ResponseData::getInstance (1,null,array(),array(),$this->request->isAjax());
                }
            }
        }
        return ResponseData::getInstance (0,'修改密码失败',array(),array(),$this->request->isAjax());
    }

    //统计设备
    public function countEquipMentView(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        $equipment = new Equipment();
        $equipType = $equipment->field('text_description')->group('text_description')->select();
        $this->assign('equipType',$equipType);
        return $this->fetch('countEquipMent');
    }

    public function countSchoolPartEquipMent(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        try{
            $schoolpart_id = $this->request->param('schoolpart_id');
            $text_description = $this->request->param('text_description');
            $size = $this->request->param('limit');
            cookie('limit', $size,60*60*24*7);
            $offset = (intval($this->request->param('page'))-1)*$size;
            $equipType = null;
            $total = 0;
            if($schoolpart_id == '')
                $schoolpart_id = '-1';
            if($text_description == '')
                $text_description = '-1';
            $res =Db::query('call countSchoolPartEquipMent(?,?,?,?)',[$schoolpart_id,$offset,$size,$text_description]);
            if(count($res)==2){
                $equipType = $res[0];
                $total = $res[1][0]['total'];
            }else{
                $equipType = [];
                $total = 0;
            }
            $data = new ResponseData(1,null,$equipType,array('total'=>count($equipType)),$this->request->isAjax());
            $data->code = 0;
            $data->count = $total;
            return $this->request->isAjax()?$data:json_encode($data);
        }catch (\Exception $e){
            $data = new ResponseData(1,null,array(),array(),$this->request->isAjax());
            $data->code = 1;
            $data->msg = $e->getMessage();
            return $this->request->isAjax()?$data:json_encode($data);
        }
    }

    public function  countCollegeEquipMent(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        try{
            $schoolpart_id = $this->request->param('schoolpart_id');
            $college_id = $this->request->param('college_id');
            $text_description = $this->request->param('text_description');
            $size = $this->request->param('limit');
            $offset = (intval($this->request->param('page'))-1)*$size;
            cookie('limit', $size,60*60*24*7);
            $equipType = null;
            $total = 0;
            if($college_id == '')
                $college_id = '-1';
            if($schoolpart_id == '')
                $schoolpart_id = '-1';
            if($text_description == '')
                $text_description = '-1';
            $res =Db::query('call countCollegeEquipMent(?,?,?,?,?)',[$schoolpart_id,$college_id,$offset,$size,$text_description]);


            if(count($res)==2){
                $equipType = $res[0];
                $total = $res[1][0]['total'];
            }else{
                $equipType = [];
                $total = 0;
            }

            $data = new ResponseData(1,null,$equipType,array('total'=>count($equipType)),$this->request->isAjax());
            $data->code = 0;
            $data->count = $total;
            return $this->request->isAjax()?$data:json_encode($data);
        }catch (\Exception $e){
            $data = new ResponseData(1,null,array(),array(),$this->request->isAjax());
            $data->code = 1;
            $data->msg = $e->getMessage();
            return $this->request->isAjax()?$data:json_encode($data);
        }
    }

    public function getCollege(){
        try{
            $schoolpart_id = $this->request->param('schoolpart_id');
            $college = new College();
            $data = $college->field('college_id,text_description')
                ->where(array('schoolpart_id'=>$schoolpart_id,'college_id'=>$this->filterCollege['college_id']))
                ->select();
//            $data = Db::query("SELECT hqgl_college.college_id,hqgl_college.text_description ".
//                "FROM hqgl_college WHERE hqgl_college.schoolpart_id = ?",[$schoolpart_id]);
            return ResponseData::getInstance (1,null,array($data),array('count'=>count($data)),
                $this->request->isAjax());
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    public function getBuilding(){
        try{
            $college_id = $this->request->param('college_id');
            $data = Db::query("SELECT hqgl_building.building_id,hqgl_building.text_description ".
                "FROM hqgl_building WHERE hqgl_building.college_id = ?",[$college_id]);
            return ResponseData::getInstance (1,null,array($data),array('count'=>count($data)),
                $this->request->isAjax());
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    public function getRoom(){
        try{
            $building_id = $this->request->param('building_id');
            $data = Db::query("SELECT hqgl_room.room_id,hqgl_room.room_num as text_description ".
                "FROM hqgl_room WHERE hqgl_room.building_id = ?",[$building_id]);
            return ResponseData::getInstance (1,null,array($data),array('count'=>count($data)),
                $this->request->isAjax());
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    public function getEquipMent(){
        try{
            $room_id = $this->request->param('room_id');
            $equiptype = $this->request->param('equiptype');
            $_arr =explode('-',$equiptype);
            if(count($_arr)>=3){
                $text_description = '';
                $split = '';
                for($i=0;$i<count($_arr)-1;$i++){
                    $text_description .= $split.$_arr[$i];
                    $split = '-';
                }
                $power = $_arr[count($_arr)-1];
            }else if(count($_arr)==2){
                $text_description = $_arr[0];
                $power = $_arr[1];
            }else{
                $text_description = '';
                $power = $_arr[0];
            }
            $equipment = new Equipment();
            $table = $equipment->field('text_description,name,brand,power,\'1\' as number,day_time')
                ->where(array('text_description'=>$text_description,'room_id'=>$room_id,'power'=>$power))
                ->limit(1)->select();
            if(!$table){
                $table = $equipment->field('text_description,name,brand,power,\'1\' as number,day_time')
                    ->where(array('text_description'=>$text_description,'power'=>$power))
                    ->limit(1)->select();
            }
            return ResponseData::getInstance (1,null,array($table),array('count'=>count($table)),
                $this->request->isAjax());
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    //设置设备
    public function setEquipMentView(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        $equipment = new Equipment();
        $equipType = $equipment->field('text_description')->group('text_description')->select();
        $this->assign('equipType',$equipType);
        return $this->fetch('setEquipMent');
    }


    public function setEquipDataTable(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        try{
            $size = $this->request->param('limit');
            $offset = (intval($this->request->param('page'))-1)*$size;
            cookie('limit', $size,60*60*24*7);

            $text_description = $this->request->param('text_description','');
            $where = $text_description==''?null:array('text_description'=>$text_description);
            $equipment = new Equipment();
            if($where){
                $equipType = $equipment->field('text_description,power,day_time')->distinct('text_description,power')->where($where)->select();
                $count = count($equipType);
            }else{
                $equipType = $equipment->field('text_description,power,day_time')->group('text_description,power')->limit($offset,$size)->select();
                $count = Db::query('SELECT COUNT(`_group_count_`.text_description) AS tp_count '.'FROM (SELECT '.
                    '`text_description` FROM `hqgl_equipment` GROUP BY text_description,power ) `_group_count_` LIMIT 1')[0]['tp_count'];
            }
            $data = new ResponseData(1,null,$equipType,array('total'=>count($equipType)),$this->request->isAjax());
            $data->code = 0;
            $data->count = $count;
            return $this->request->isAjax()?$data:json_encode($data);
        }catch (\Exception $e){
            $data = new ResponseData(1,null,array(),array(),$this->request->isAjax());
            $data->code = 1;
            $data->msg = $e->getMessage();
            return $this->request->isAjax()?$data:json_encode($data);
        }
    }

    public function updateEquipDayTime(){
        $response = $this->checkAdmin();
        if(json_decode($response,true)['status'] == 0){
            return $response;
        }
        try{
            $text_description = $this->request->param('text_description','');
            $power = $this->request->param('power','');
            $power = intval($power);
            $day_time = $this->request->param('day_time');
            $day_time = intval($day_time);
            $equipment = new Equipment();
            $n = $equipment->isUpdate(true)->save(array('day_time'=>$day_time),array(
                'text_description'=>$text_description,'power'=>$power));
            if($n>0){
                return ResponseData::getInstance (1,'修改成功',array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,'修改失败',array(),array(),$this->request->isAjax());
            }
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    public function createTopUserGroup(){
        $schoolpart = new Schoolpart();
        $college = new College();
        $_id = $schoolpart->field('schoolpart_id')->select();
        $schoolpart_id = [];

        foreach ($_id as $key=>$value){
            $schoolpart_id[] = $value['schoolpart_id'];
        }

        $_id = $college->field('college_id')->select();
        $college_id = [];

        foreach ($_id as $key=>$value){
            $college_id[] = $value['college_id'];
        }

        $authorization = ['schoolpart'=>[
            'action'=>[],'id'=>$schoolpart_id,'full'=>true
        ],'college'=>[
            'action'=>[],'id'=>$college_id
        ]];
        $res = Db::table(config('database.prefix').'usergroup')
        ->insert(array(
            'text_description'=>'顶级用户组',
            'usergroup_id'=>create_guid(),
            'user_id'=>'',
            'authorization'=>json_encode($authorization)
        ));
    }

    public function showMessage(){

    }

    //通知，界面
    public function message_2(){
       return $this->fetch('message_2');
    }

    //未读消息
    public function unRead(){
        $where = array();
        $where['status'] = 1;
        return $this->getMessage($where);
    }

    //未处理消息
    public function unHandle(){
        $where = array();
        $where['status'] = 2;
        return $this->getMessage($where);
    }

    //历史记录
    public function history(){
        $where = array();
        $where['status'] = array('>',2);
        return $this->getMessage($where);
    }

    public function getMessage(array $where){

        try{
            $size = $this->request->param('limit');
            $offset = (intval($this->request->param('page'))-1)*$size;
            $record = new Record();
            if($this->admin){
                $where['response_id'] = session('user.user_id');
                $table = $record->alias('record')
                    ->field('record_id,text_description,power,number,_create_time,status,type,user.user_name')
                    ->where($where)
                    ->join($this->prefix.'user user','record.response_id = user.user_id')
                    ->order('_create_time')
                    ->limit($offset,$size)->select();
            }else{
                $where['request_id'] = session('user.user_id');
                $table = $record->alias('record')
                    ->field('record_id,text_description,power,number,_create_time,status,type,user.user_name')
                    ->where($where)
                    ->join($this->prefix.'user user','record.response_id = user.user_id')
                    ->order('_create_time')
                    ->limit($offset,$size)->select();
            }
            $count = $record->field('count(*) as `count`')->where($where)->select()[0]['count'];
            $data = new ResponseData(1,null,$table,array('total'=>count($table)),$this->request->isAjax());
            $data->code = 0;
            $data->count = $count;
            return $this->request->isAjax()?$data:json_encode($data);
        }catch (\Exception $e){

            $data = new ResponseData(1,null,array(),array(),$this->request->isAjax());
            $data->code = 1;
            $data->msg = $e->getMessage();
            return $this->request->isAjax()?$data:json_encode($data);
        }
    }
    //新增设备
    public function addEquipMentView(){
        $equipment = new Equipment();
        $equipType = $equipment->field('text_description,power')->group('text_description,power')->select();
        $this->assign('equipType',$equipType);
        return $this->fetch('addEquipMent');
    }

    //尝试新增设备，等待一级账号审核
    public function tryAddEquipMent(){
        try{
            $usergroup = new Usergroup();
            $data = $this->request->param(true);
            $table = $usergroup
                ->field('user_id')->where(array('usergroup_id'=>session('user.usergroup_id')))->select();
            $data['record_id'] = create_guid();
            $data['_create_time'] = date('Y-m-d H:i:s');
            $data['request_id'] = session('user.user_id');
            $data['response_id'] = $table[0]['user_id'];
            unset($data['/index/tryAddEquipMent']);
            unset($data['equipmentType']);
            $record = new Record();
//            $equipment = new Equipment();
//            $max = $equipment->field('number')
//                ->where(array('text_description'=>$data['text_description'],'room_id'=>$data['room_id']))->select();
//            $max =intval($max[0]['number']);
//            $_tmp = $record->field('number')
//                ->where(array('text_description'=>$data['text_description'],'room_id'=>$data['room_id']))->select();
//            if(count($_tmp)>0){
//                if($max<intval($_tmp[0]['number']+intval($data['number']))){
//                    return ResponseData::getInstance (0,'',array(),array(),$this->request->isAjax());
//                }
//            }
            $n = $record->insert($data);
            if($n>0){
                return ResponseData::getInstance (1,'申报成功，耐心等候~',array(),array(),$this->request->isAjax());
            }else{
                return ResponseData::getInstance (0,$record->getError(),array(),array(),$this->request->isAjax());
            }
        }catch (\Exception $e){
            return ResponseData::getInstance (0,$e->getMessage(),array(),array(),$this->request->isAjax());
        }
    }

    //报废设备 ，视图
    public function removeEquipMentView(){
        return $this->fetch('removeEquipMent');
    }

    public function queryEquipMentByRoom(){
        try{
            $size = $this->request->param('limit');
            $offset = (intval($this->request->param('page'))-1)*$size;
            $equipment = new Equipment();
            $table = $equipment->where(array('room_id'=>$this->request->param('room_id')))
                ->limit($offset,$size)->select();
            $count = $equipment->where(array('room_id'=>$this->request->param('room_id')))->count();

            $data = new ResponseData(1,null,$table,array('total'=>count($table)),$this->request->isAjax());
            $data->code = 0;
            $data->count = $count;
            return $this->request->isAjax()?$data:json_encode($data);
        }catch (\Exception $e){
            $data = new ResponseData(1,null,array(),array(),$this->request->isAjax());
            $data->code = 1;
            $data->msg = $e->getMessage();
            return $this->request->isAjax()?$data:json_encode($data);
        }
    }

}
