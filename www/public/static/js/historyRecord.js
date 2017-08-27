/**
 * Created by lx on 2017/8/27.
 */

require(["jquery","layui"],function($) {
    $(function () {

        layui.config({
            dir: '/static/layui/'
        });
        ShowPersonRepairStatus = {
            "1": "申请中",
            "2": "申请通过",
            "3": "修好了,可以再次使用",
            "4": "驳回"
        }

        layui.use(['table', 'laypage', 'element', 'laytpl'], function () {
            var laypage = layui.laypage;
            var table = layui.table;
            var element = layui.element;
            var historyObj = new window.WkkyData('/index/historyRecord', {
                credentials: 'include',
                method: "POST"
            }, {page: '1'});
            historyObj.setOnSuccess(function (handleResponse) {
                // console.log(handleResponse.getData());
                var tableIns = table.render({
                    elem: '#historyTable' //指定原始表格元素选择器（推荐id选择器）
                    , cols: [[{field: 'index', title: '编号', align: 'center', width: 80, sort: true}
                        , {field: 'request_username', title: '申请人', width: 80}
                        , {field: 'response_username', title: '审核人', width: 80}
                        , {field: 'timer', title: '申请时间', width: 80}
                        , {field: 'applayAdress', title: '申请人地址', width: 100}
                        , {field: 'applyContent', title: '申请内容', width: 80, templet: '#applyContent'}
                        , {field: 'status', title: '申请状态', width: 177}
                    ]],
                    data: $.each(handleResponse.getData(), function (index, item) {
                        item['index'] = index + 1;
                        item['status'] = ShowPersonRepairStatus[item['status']];
                        item['applayAdress'] = item['shoolName'] + " " + item['collegeName'] + " " + item['buildName'] + " " + item['roomName'];
                        item['applyContent'] = item['dataRepair'];
                    }),
                    page: false,

                });

                laypage.render({
                    elem: 'lucas-history' //注意，这里的 test1 是 ID，不用加 # 号
                    , count: handleResponse.getExtra('total')
                    , limit: 2
                    , jump: function (obj, first) {
                        var foo = new FormData();
                        foo.append('page', obj.curr);
                        myInit = {method: 'POST', credentials: 'include', body: foo};
                        var request2 = new Request('/index/historyRecord', myInit);
                        historyObj.setRequest(request2);
                        historyObj._success = [];
                        historyObj.setOnSuccess(function (handleResponse) {
                            tableIns.reload({
                                data: $.each(handleResponse.getData(), function (index, item) {
                                    item['index'] = index + 1;
                                    item['status'] = ShowPersonRepairStatus[item['status']]
                                    item['applayAdress'] = item['shoolName'] + " " + item['collegeName'] + " " + item['buildName'] + " " + item['roomName'];
                                    item['applyContent'] = item['dataRepair'];
                                }),
                            });
                        })
                        historyObj.getDataFormRemote();
                        //首次不执行
                        if (!first) {
                        }
                    }
                });
            })
            historyObj.getDataFormRemote();
        })
    })
})
