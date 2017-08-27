/**
 * Created by lx on 2017/8/24.
 */
require(["jquery","layui"],function($){
    $(function () {

        layui.config({
            dir: '/static/layui/'
        });
        window.lucasShowPersonRepairStatus={
            "1":"申请中",
            "2":"申请通过",
            "3":"修好了,可以再次使用",
            "4":"驳回"
        }

        layui.use(['table','laypage','element','laytpl'], function(){
            var laypage = layui.laypage;
            var table = layui.table;
            var element = layui.element;


            var initObj = new window.WkkyData('/index/initAuditingInfo', {
                credentials: 'include',
                method: "POST"
            }, {page:'1'});
            initObj.setOnSuccess(function (handleResponse) {
                console.log(handleResponse.getData());
                var tableIns =table.render({
                    id:'newRecord',
                    elem: '#auditingTable' //指定原始表格元素选择器（推荐id选择器）
                    , cols: [[{field: 'index',title: '编号', align: 'center', width: 80, sort: true}
                        , {field: 'request_username', title: '申请人', width: 80}
                        , {field: 'response_username', title: '审核人', width: 80}
                        , {field: 'timer', title: '申请时间', width: 80}
                        , {field: 'applayAdress', title: '申请人地址', width: 100}
                        , {field: 'applyContent', title: '申请内容', width: 80,templet:'#applyContent'}
                        , {field: 'status', title: '申请状态', width: 177}
                        ,{fixed: 'right',title: '操作', width:150, align:'center', toolbar: '#barNewRecord'}
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
                    elem: 'lucas-paging' //注意，这里的 test1 是 ID，不用加 # 号
                    ,count: handleResponse.getExtra('total')
                    ,limit: 2
                    ,jump: function(obj, first){
                        var fo = new FormData();
                        fo.append('page',obj.curr);
                        myInit = { method: 'POST', credentials: 'include',body:fo};
                        var myRequest = new Request('/index/initAuditingInfo',myInit);
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


                table.on('tool(newRecord)', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象

                    if(layEvent === 'detail'){ //查看
                        window.location='/index/showLookInfo/dd/张三';
                        //do somehing
                    } else if(layEvent === 'pass'){ //删除
                        // console.log("对当前"+data);
                        layer.confirm('确定审核通过吗', function(index){
                            data['status']='2';
                            console.log("");
                            console.log("查询", data)
                            var results=responseSimpleData(data,1);
                            if(results == "ok"){
                                $('.layui-table-body tr').eq(data['index'] -1).last("td").find("a").eq(1).addClass('lucasDis');
                            }
                        });
                    } else if(layEvent === 'noPass'){ //编辑
                        //do something
                        layer.confirm('确定拒绝申请吗', function(index){
                            // obj.del(); //删除对应行（tr）的DOM结构
                            layer.close(index);
                            //向服务端发送删除指令
                        });
                        //同步更新缓存对应的值
                        obj.update({
                            username: '123'
                            ,title: 'xxx'
                        });
                    }
                });

            })
            initObj.getDataFormRemote();

            element.on('tab(docDemoTabBrief)', function(data){
                console.log(this); //当前Tab标题所在的原始DOM元素
                console.log(data.index); //得到当前Tab的所在下标
                console.log(data.elem); //得到当前的Tab大容器
            });
            //传送数据到后台
            function responseSimpleData(tempData,flag) {
                console.log(tempData);
                if(flag == 1){
                    var applyPassObj = new window.WkkyData('/index/applyPass', {
                        credentials: 'include',
                        method: "POST"
                    }, {data:JSON.stringify(tempData),kind:tempData["kind"]});
                    applyPassObj.setOnSuccess(function (handleResponse) {
                        if(handleResponse.getExtra("status") == "ok"){
                            return "ok";
                        }else{
                            alert("系统发生错误，请联系管理员");
                            return "error";
                        }
                    });
                    applyPassObj.getDataFormRemote();


                }

            }

        });
























    });

});