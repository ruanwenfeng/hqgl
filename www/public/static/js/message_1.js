/**
 * Created by Administrator on 2017/9/13.
 */
/**
 * Created by Administrator on 2017/8/21.
 */
window.require(['jquery','layui','cookie'],function ($) {
    $(function () {
        window.curr_year = $.cookie('curr_year');
        window.load = null;
        window.flag = 0;
        layui.config({
            dir: '/static/layui/'
        });
        layui.use(['element','table','layer'], function(){
            var table = layui.table;

            var unRead = new WkkyData();
            unRead.setOnBefor(befor);
            unRead.setOnSuccess(function (response) {

            });
            unRead.setOnFail(fail);
            unRead.setOnError(error);
            unRead.setOnAfter(after);

            var unHandle = new WkkyData();
            unHandle.setOnBefor(befor);
            unHandle.setOnSuccess(function (response) {

            });
            unHandle.setOnFail(fail);
            unHandle.setOnError(error);
            unHandle.setOnAfter(after);


            var history = new WkkyData();
            history.setOnBefor(befor);
            history.setOnSuccess(function (response) {

            });
            history.setOnFail(fail);
            history.setOnError(error);
            history.setOnAfter(after);

            var record = new WkkyData();
            var record2 = new WkkyData();
            record2.setOnBefor(befor);

            record2.setOnFail(fail);
            record2.setOnError(error);
            record2.setOnAfter(after);


            var element = layui.element;
            element.on('tab', change_tab);
            $('.layui-tab-title li').eq(0).click();
            function befor(response) {
                loading();
            }
            function fail(response) {
                layer.msg(response.getMessage(),function () {

                });
            }
            function error(response) {
                layer.msg('系统错误',function () {

                });
            }
            function after(response) {
                closeLoad(1);
            }

            function change_tab(e){
                loading();
                var url ='';
                switch (e.index){
                    case 0:
                        url = '/index/unRead';
                        break;
                    case 1:
                        url = '/index/unHandle';
                        break;
                    case 2:
                        url = '/index/history';
                        break;
                }
                table.render({
                    elem: '#data-table'
                    ,cols:  [[ //标题栏
                        {field: 'index', title: '编号', align: 'center',width: 80},
                        {field: 'user_name', title: '申报人', width: 150}
                        ,{field: 'text_description', title: '设备描述',  width: 180}
                        ,{field: 'power', title: '功率（w）',  width: 120}
                        ,{field: 'number',title:'数量',width: 80}
                        ,{field: 'type',title:'申报类型',width: 100,sort: true,templet:'#my-1'}
                        ,{field: '_create_time',title:'申报时间',width: 180,sort: true}
                        ,{field: 'status',title:'状态',width: 80,templet:'#my-2'}
                        ,{title:'操作',width: 80,align: 'center',templet:'#my-3'}
                    ]], //设置表头
                    method:'post',
                    done:function () {
                        closeLoad(1);
                    },
                    limit:typeof $.cookie('limit') === typeof  undefined ? 30 :$.cookie('limit'),
                    page: true,
                    even:true,
                    url:url,
                    loading: true
                });
                table.on('tool',function (obj) {

                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象
                    var option = {
                        area:['100%','100%'],
                        title:'申报信息详情',
                        type: 2
                        ,offset: 'auto' //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
                        ,id: 'layerDemo' //防止重复弹出
                        ,content:'/index/messageDetail/record_id/'+data['record_id']
                        ,btnAlign: 'c' //按钮居中
                        ,shade: 0.5 //不显示遮罩
                    };
                    var reflush;
                    function add_option() {
                        option['btn'] = ['批准', '驳回'];
                        option['btn1']=function (e) {
                            record2.setRequest(new Request('/index/updateMessage/status/3/record_id/'+data['record_id'],{
                                credentials: 'include',
                                method: "POST"
                            }));
                            record2._success = [];
                            record2.setOnSuccess(function (response) {
                                layer.msg('操作成功');
                                reflush&&reflush();
                                layer.close(index);
                            });
                            record2.getDataFormRemote();
                        };
                        option['btn2']=function (e) {
                            record2.setRequest(new Request('/index/updateMessage/status/4/record_id/'+data['record_id'],{
                                credentials: 'include',
                                method: "POST"
                            }));
                            record2._success = [];
                            record2.setOnSuccess(function (response) {
                                layer.msg('操作成功');
                                reflush&&reflush();
                                layer.close(index);
                            });
                            record2.getDataFormRemote();
                        }
                    }
                    if(data['status'] === 1){
                        record.setRequest(new Request('/index/updateMessage/status/2/record_id/'+data['record_id'],{
                            credentials: 'include',
                            method: "POST"
                        }));
                        record.getDataFormRemote();
                        $('.layui-tab-title li').eq(0).click();
                        add_option();
                    }else if(data['status']===2){
                        reflush =function () {
                            $('.layui-tab-title li').eq(1).click();
                        };
                        add_option();
                    }
                    var index = layer.open(option);
                })
            }
        });
    });

});