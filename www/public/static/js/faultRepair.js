/**
 * Created by lx on 2017/8/22.
 */
require(["jquery","layui"],function($){
    $(function () {
        layui.config({
            dir: '/static/layui/'
        });
        window.equipmentStatus={
            "1":"正常使用",
            "2":"损坏"
        }
        $("#extraDiv #confirmRepair").hide();
        layui.use(['form','element','table','layer'],function () {
        var form = layui.form
        var ele =layui.element;
        var table = layui.table;
        var laypage = layui.laypage;
        var layer = layui.layer;
        //更新校区
        function updateSchool() {
            var school = new window.WkkyData('/index/faultRepairSchool',{
                credentials: 'include',
                method: "POST"
            },null);
            school.setOnSuccess(function (handleResponse) {
                var schoolArray=handleResponse.getData();
                $.each(handleResponse.getData(),function (index, schoolObj) {
                    $("#schoolSelect").append($("<option>"+schoolObj['text_description']+"</option>"));

                });
                form.render('select');
                form.on('select(schoolSelectFilter)', function(data){
                    $("#collegeSelect option").remove();
                    var schoolIndex=data.othis.find("dl > dd.layui-this").index();
                    updateCollege(schoolArray[schoolIndex]['schoolpart_id']);
                });

            });
            school.getDataFormRemote();
        }
        //更新学院
        function updateCollege(scholId) {
            var college = new window.WkkyData('/index/queryCollege',{
                credentials: 'include',
                method: "POST"
            },{schoolpart_id:scholId});
            college.setOnSuccess(function (handleResponse) {
                console.log({"学院":handleResponse.getData()});
                var collegeArray=handleResponse.getData();
                $.each(collegeArray,function (index, collegeObj) {
                    $("#collegeSelect").append($("<option>"+collegeObj['text_description']+"</option>"));
                });
                form.render('select');
                form.on('select(collegeSelectFilter)', function(data){
                    $("#buildingSelect option").remove();
                    var collegeIndex=data.othis.find("dl > dd.layui-this").index();
                    updateBiilding(collegeArray[collegeIndex]['schoolpart_id'],collegeArray[collegeIndex]['college_id']);
                });
            });
            college.getDataFormRemote();
        }

        //更新楼栋
        function updateBiilding(scholId,collegeId) {
            var building = new window.WkkyData('/index/queryBuilding',{
                credentials: 'include',
                method: "POST"
            },{schoolpart_id:scholId,college_id:collegeId});
            building.setOnSuccess(function (handleResponse) {
                console.log({"楼栋":handleResponse.getData()});
                var buildingArray=handleResponse.getData();
                $.each(buildingArray,function (index, buildingObj) {
                    $("#buildingSelect").append($("<option>"+buildingObj['text_description']+"</option>"));
                });
                form.render('select');
                form.on('select(buildingSelectFilter)', function(data){
                    $("#roomSelect option").remove();
                    var buildingIndex=data.othis.find("dl > dd.layui-this").index();
                    // alert(buildingArray[buildingIndex]['schoolpart_id'] + "和" +buildingArray[buildingIndex]['college_id'] +"和" +buildingArray[buildingIndex]['building_id'] );
                    updateRoom(buildingArray[buildingIndex]['schoolpart_id'],buildingArray[buildingIndex]['college_id'],buildingArray[buildingIndex]['building_id']);
                });


            });
            building.getDataFormRemote();
        }
        //更新房间
        function updateRoom(scholId,collegeId,buildingId) {
            var rooms = new window.WkkyData('/index/queryRoom',{
                    credentials: 'include',
                    method: "POST"
                },{schoolpart_id:scholId,college_id:collegeId,building_id:buildingId});
                rooms.setOnSuccess(function (handleResponse) {
                    console.log({"房间":handleResponse.getData()});
                    var roomArray=handleResponse.getData();
                    $.each(roomArray ,function (index,roomObj) {
                        $("#roomSelect").append($("<option>"+roomObj['room_num']+"</option>"));
                    })
                    form.render('select');
                    form.on('select(roomSelectFilter)', function(data){
                        var roomIndex=data.othis.find("dl > dd.layui-this").index();
                        // updateEquipments(roomArray[roomIndex]['schoolpart_id'],roomArray[roomIndex]['college_id'],roomArray[roomIndex]['building_id'],roomArray[roomIndex]['room_id']);
                        updateEquipments(roomArray[roomIndex]['room_id'],roomArray[roomIndex]['schoolpart_id'],roomArray[roomIndex]['college_id'],roomArray[roomIndex]['building_id']);

                    });
                });
                rooms.getDataFormRemote();
        }
        //显示设备信息  function updateEquipments(scholId,collegeId,buildingId,roomId)
        function updateEquipments(roomId,scholId,collegeId,buildingId) {
            var equipments = new window.WkkyData('/index/lucasQueryEquipment', {
                credentials: 'include',
                method: "POST"
            }, {room_id: roomId,page:1});
            equipments.setOnSuccess(function (handleResponse) {
                var t=table.render({
                    elem: '#equipmentTable'
                    , cols: [[
                        {checkbox: true, fixed: true}
                        , {field: 'index',title: '编号', align: 'center', width: 80, sort: true}
                        , {field: 'text_description', title: '设备描述', width: 80}
                        , {field: 'power', title: '功率(单位 w)', width: 80}
                        , {field: 'status', title: '设备状态', width: 177}
                    ]],
                    data: $.each(handleResponse.getData(), function (index, item) {
                        item['index'] = index + 1;
                        item['status'] = window.equipmentStatus[item['status']];
                    }),
                   page: true,
                   even: true,
                });
                var lucasSelected;
                $("#extraDiv #confirmRepair").click(function () {
                    showFaultRepairInfo(lucasSelected,roomId,scholId,collegeId,buildingId);
                });
                table.on('checkbox', function(e){
                    var checkStatus = table.checkStatus(t.config['data']);
                    if(checkStatus.data.length > 0){
                        $("#extraDiv #confirmRepair").show();
                    }else{
                        $("#extraDiv #confirmRepair").hide();
                    }
                    lucasSelected=checkStatus;

                });
            });

            equipments.getDataFormRemote();
        }
        //确认报修设备信息
        function showFaultRepairInfo(data,roomId,scholId,collegeId,buildingId) {
            var finalSelect=[];
            var showData;
            if(typeof  data == "undefined"){
                showData="请选择你要报修的设备";

            }else{
                showData="<div style='padding: 50px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;'><h>"+"请确认你要报修的设备"+"</h>";
                $.each(data.data,function (index, dataObj) {
                    var temp2=dataObj.text_description.trim();
                    var flags = false;
                    for(var j = 0;j < finalSelect.length;j++ ){
                        if(finalSelect[j]["text_description"] == temp2){
                            finalSelect[j]["amount"] ++;
                            flags = true;
                            break;
                        }
                    }
                    if(!flags){
                        var tempObj={
                            "amount":1,
                            "text_description": temp2,
                            "roomId" :roomId,
                            "scholId":scholId,
                            "collegeId":collegeId,
                            "buildingId":buildingId
                        }
                        finalSelect.push(tempObj);
                    }
                })
                $.each(finalSelect,function (index, dataObj) {
                    var tempStr=("<br/><b>"+dataObj.amount+"　个"+dataObj.text_description+"</b><br/>");
                    showData += tempStr;
                });
                showData+="</div>";
            }
            layer.open({
                type: 1
                ,title: false //不显示标题栏
                ,closeBtn: false
                ,area: '300px;'
                ,shade: 0.8
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['确定报修', '重新选择']
                ,moveType: 1 //拖拽模式，0或者1
                ,content: showData
                ,success: function(layero){

                }
                ,yes: function(index, layero){
                    if(finalSelect.length > 0){
                        layer.close(index);

                        sendFaultRepairRequest(finalSelect);
                    }


                //按钮【按钮一】的回调
                }
                ,btn2: function(index, layero){
                //按钮【按钮二】的回调
                //return false 开启该代码可禁止点击该按钮关闭
                }
            });
        }

        //发送报修请求
        function sendFaultRepairRequest(faultEquipmentInfo) {
            var reqparam=JSON.stringify(faultEquipmentInfo);
            window.open("/index/showReason/flag/1/request/"+reqparam);
            // var faultRepairRequest = new window.WkkyData('/index/showReason',{
            //     credentials: 'include',
            //     method: "POST"
            // },{equipment_id:faultEquipmentInfo});
            // faultRepairRequest.setOnSuccess(function (handleResponse) {
            // })
            // faultRepairRequest.getDataFormRemote();
        }
        updateSchool();
    });
    })

})