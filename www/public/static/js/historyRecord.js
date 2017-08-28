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
                console.log("ddd",handleResponse);
                var tableIns = table.render({
                    id:'hhdeal',
                    elem: '#dealTable' //指定原始表格元素选择器（推荐id选择器）
                    , cols: [[{field: 'index', title: '编号', align: 'center', width: 80, sort: true}
                        , {field: 'request_username', title: '申请人', width: 80}
                        , {field: 'response_username', title: '审核人', width: 80}
                        , {field: 'timer', title: '申请时间', width: 80}
                        , {field: 'applayAdress', title: '申请人地址', width: 100}
                        , {field: 'applyContent', title: '申请内容', width: 80, templet: '#applyContent'}
                        , {field: 'status', title: '申请状态', width: 177}
                        ,{fixed:'right',title: '操作', width:150, align:'center', toolbar: '#barDeal'}
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
                            table.on('tool(deal)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                                var data = obj.data; //获得当前行数据
                                var layEvent = obj.event; //获得 lay-event 对应的值
                                var tr = obj.tr; //获得当前行 tr 的DOM对象
                                if(layEvent === 'continueUse'){ //删除
                                    layer.confirm('确定继续使用', function(index){
                                        data['status']='3';
                                        $('.layui-table-body tr').eq(data['index'] -1).last("td").find("a").eq(0).addClass('lucasDis');
                                        responseSimpleData(data,1);
                                        layer.close(index);
                                    });
                                }
                            });


                        })
                        historyObj.getDataFormRemote();
                        //首次不执行
                        if (!first) {
                        }
                    }
                });


                table.on('tool(hhdeal)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象
                   if(layEvent === 'continueUse'){ //删除
                        layer.confirm('确定继续使用', function(index){
                            data['status']='3';
                            console.log(data);
                            $('.layui-table-body tr').eq(data['index'] -1).last("td").find("a").eq(0).addClass('lucasDis');
                            responseSimpleData(data);
                            layer.close(index);
                        });
                    }
                });

            })
            historyObj.getDataFormRemote();

            function responseSimpleData(tempData) {
                    var repairObj = new window.WkkyData('/index/rePairCulateE', {
                        credentials: 'include',
                        method: "POST"
                    }, {data:JSON.stringify(tempData),kind:tempData["kind"]});
                    repairObj.setOnSuccess(function (handleResponse) {
                        if(handleResponse.getExtra("status") == "ok"){
                            layer.msg('已经重新开始计算电费', {
                                icon: 1,
                                time: 2000 //2秒关闭（如果不配置，默认是3秒）
                            }, function(){
                                //do something
                            });
                        }else{
                            layer.alert('系统发生异常，请联系管理员');
                        }
                    });
                    repairObj.getDataFormRemote();


            }




        })
    })
})
