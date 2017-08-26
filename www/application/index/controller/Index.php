<?php
namespace app\index\controller;

use app\extend\ResponseData;
use app\index\model\Building;
use app\index\model\College;
use app\index\model\Equipment;
use app\index\model\Room;
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
        session('user.usergroup_id',3);
        session('user.user_name','wkky');

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
                return ResponseData::getInstance(1, null, array(), array("result" => "error"), $this->request->isAjax());
            }


            return ResponseData::getInstance(1, null, array(), array("result" => "ok"), $this->request->isAjax());
        }
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
    //显示个人设备报修，及审核信息
    public function  showPersonRepair(){
        return $this->fetch();
    }
//    //加载个人记录
//    public function loadingInfo(){
//        $uerId=session('user.user_id');
//        Db::table(config('database.prefix').'requestrecord') ->where(array("request_user"=>$uerId))->whereOr(array("response_user"=>$uerId))->select();
//
//    }
    //审核信息初始化  即个人记录
    public function initAuditingInfo(){
        $uerId=session('user.user_id');
        $whereNew = array("request_user"=>$uerId,"status" =>1);
        $table = Db::table(config('database.prefix').'viewrequestcord') ->where($whereNew)->whereOr(array("response_user"=>$uerId))
            ->where("UNIX_TIMESTAMP(timer)",">=", strtotime(date("Y-m-d h:i:s",strtotime("-5 day"))))
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
            ->where("UNIX_TIMESTAMP(timer)",">=", strtotime(date("Y-m-d h:i:s",strtotime("-5 day"))))->distinct(true)
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
    public function  showReason(){
        $flag=$this->request->param("flag");
        $requestObj=null;
        if($flag == 1){
            $requestStr=$this->request->param("request");
            $requestObj=json_decode($requestStr,true);
            $room ="";
            $scholId = "";
            $collegeId = "";
            $buildingId= "";
            foreach ( $requestObj[0] as $key => $obj){
                if($key == "roomId"){
                    $room=$obj;
                }else if($key == "scholId"){
                    $scholId=$obj;
                }else if($key == "collegeId"){
                    $collegeId=$obj;
                }else if($key == "buildingId"){
                    $buildingId=$obj;
                }
            }
            $roomText=Db::table(config('database.prefix').'room')->where(array("room_id"=>$room))->value("room_num");
            $SchoolText=Db::table(config('database.prefix').'schoolpart')->where(array("schoolpart_id"=>$scholId))->value("text_description");
            $collegeText=Db::table(config('database.prefix').'college')->where(array("college_id"=>$collegeId))->value("text_description");
            $buildingText=Db::table(config('database.prefix').'building')->where(array("building_id"=>$buildingId))->value("text_description");
            for($i = 0;$i < count($requestObj) ;$i ++){
                $requestObj[$i]['roomDescript'] = $roomText;
                $requestObj[$i]['scholDescript'] = $SchoolText;
                $requestObj[$i]['collegeDescript'] = $collegeText;
                $requestObj[$i]['buildingDescript'] = $buildingText;
            }
        }
        $this->assign("flag",$flag);
        $this->assign("data",$requestObj);
        return $this->fetch();
    }
}
