/**
 * Created by lx on 2017/8/25.
 */
window.require(['jquery','layui'],function ($) {
    layui.config({
        dir: '/static/layui/'
    });
    layui.use(['element'], function(){

        var element = layui.element;

        $(function () {
            function getData() {
                var postData=[];
                $(".showPostData").each(function (index) {
                    var text_description=$(this).find(".left-info .descript").attr("values");
                    var amount=$(this).find(".left-info .amount").attr("values");
                    var school=$(this).find(".left-info .school").attr("values");
                    var building=$(this).find(".left-info .building").attr("values");
                    var college=$(this).find(".left-info .college").attr("values");
                    var room=$(this).find(".left-info .room").attr("values");
                    var reasonText=$(this).find(".right-reason .text-reason").val();

                    var schoolText=$(this).find(".left-info .school").attr("keys");
                    var buildingText=$(this).find(".left-info .building").attr("keys");
                    var collegeText=$(this).find(".left-info .college").attr("keys");
                    var roomText=$(this).find(".left-info .room").attr("keys");
                    var tempData={
                        'text_description':text_description,
                        'amount':amount,
                        'school':school,
                        'building':building,
                        'college':college,
                        'room':room,
                        'reasonText':reasonText,
                        'schoolText':schoolText,
                        'buildingText':buildingText,
                        'collegeText':collegeText,
                        'roomText':roomText,
                    }
                    postData.push(tempData);
                })
                return JSON.stringify(postData);
            }
            $("#lucasSubmit").click(function () {
                var flags=$(this).attr("values");
                if(flags == 1){
                    var sendRepair = new window.WkkyData('/index/repairEquipment',{
                        credentials: 'include',
                        method: "POST"
                    },{flags:flags,requestData:getData()});
                    sendRepair.setOnSuccess(function (handleResponse) {
                        if(handleResponse.getExtra('result') == 'ok'){
                            window.location="/index/showPersonRepair";

                        }else{
                            alert("对不起系统繁忙中");
                        }
                    });
                    sendRepair.getDataFormRemote();
                }
            })

        })
    });

})
