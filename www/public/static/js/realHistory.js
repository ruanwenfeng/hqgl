/**
 * Created by lx on 2017/8/27.
 */
/**
 * Created by lx on 2017/8/24.
 */
require(["jquery","layui"],function($){
    $(function () {

        layui.config({
            dir: '/static/layui/'
        });
        lucasShowPersonRepairStatus={
            "1":"申请中",
            "2":"申请通过",
            "3":"修好了,可以再次使用",
            "4":"驳回"
        }

        layui.use(['table','laypage','element','laytpl'], function(){
            var laypage = layui.laypage;
            var table = layui.table;
            var element = layui.element;


            var initObj = new window.WkkyData('/index/realHistory', {
                credentials: 'include',
                method: "POST"
            }, {page:'1'});
            initObj.setOnSuccess(function (handleResponse) {
                var tableIns =table.render({
                    elem: '#realHistoryTable' //指定原始表格元素选择器（推荐id选择器）
                    , cols: [[{field: 'index',title: '编号', align: 'center', width: 80, sort: true}
                        , {field: 'request_username', title: '申请人', width: 80}
                        , {field: 'response_username', title: '审核人', width: 80}
                        , {field: 'timer', title: '申请时间', width: 80}
                        , {field: 'applayAdress', title: '申请人地址', width: 100}
                        , {field: 'applyContent', title: '申请内容', width: 80,templet:'#applyContent'}
                        , {field: 'status', title: '申请状态', width: 177}
                    ]],
                    data: $.each(handleResponse.getData(), function (index, item) {
                        item['index'] = index + 1;
                        item['status'] = lucasShowPersonRepairStatus[item['status']];
                        item['applayAdress'] = item['shoolName']+" "+item['collegeName']+" "+item['buildName']+" "+item['roomName'];
                        item['applyContent'] = item['dataRepair'];
                    }),
                    page: false,

                });

                laypage.render({
                    elem: 'realHistory' //注意，这里的 test1 是 ID，不用加 # 号
                    ,count: handleResponse.getExtra('total')
                    ,limit: 2
                    ,jump: function(obj, first){
                        var fo = new FormData();
                        fo.append('page',obj.curr);
                        myInit = { method: 'POST', credentials: 'include',body:fo};
                        var myRequest = new Request('/index/realHistory',myInit);
                        initObj.setRequest(myRequest);
                        initObj._success =[];
                        initObj.setOnSuccess(function (handleResponse) {
                            tableIns.reload({
                                data: $.each(handleResponse.getData(), function (index, item) {
                                    item['index'] = index + 1;
                                    item['status'] = lucasShowPersonRepairStatus[item['status']]
                                    item['applayAdress'] = item['shoolName']+" "+item['collegeName']+" "+item['buildName']+" "+item['roomName'];
                                    item['applyContent']=item['dataRepair'];
                                }),
                            });

                        })
                        initObj.getDataFormRemote();
                        //首次不执行
                        if(!first){
                        }
                    }
                });



            })
            initObj.getDataFormRemote();



        });
























    });

});