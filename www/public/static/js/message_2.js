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

            var element = layui.element;
            element.on('tab', function(e){
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
                        {field: 'user_name', title: '审核人', width: 150}
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
                    limit:typeof $.cookie('limit') == typeof  undefined ? 30 :$.cookie('limit'),
                    page: true,
                    even:true,
                    url:url,
                    loading: true
                });
            });
            
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
        });
    });

});