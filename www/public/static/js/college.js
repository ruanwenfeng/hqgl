/**
 * Created by Administrator on 2017/8/21.
 */
window.require(['jquery','layui','highcharts'],function ($) {
    $(function () {
        layui.config({
            dir: '/static/layui/'
        });
        var schoolpart_id = $('.main-div-body').attr('data-schoolpart-id');
        layui.use(['element','table'], function(){
            var element = layui.element,table = layui.table;
            var college = new window.WkkyData('/index/queryCollege',{
                credentials: 'include',
                method: "POST"
            },{schoolpart_id:schoolpart_id});
            college.setOnSuccess(function (handleResponse) {
                table.render({
                    elem: '#college-table'
                    ,cols:  [[ //标题栏
                         {checkbox: true,width:200}
                        ,{field: 'index', title: '编号', align: 'center',width: 80}
                        ,{field: '校区名称', title: '校区名称', width: 150}
                        ,{field: 'text_description', title: '学院（部门）名称', width: 200}
                        ,{title:'操作',width: 160,align: 'center',toolbar:'#actionBar',fixed:'right'}

                    ]], //设置表头
                    done:function (res, curr, count) {

                    },
                    data:$.each(handleResponse.getData(),function (index, item) {
                            item['index'] = index+1;
                        }),
                    even:true
                });
                table.on('tool', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象
                    if(layEvent === 'detail'){ //查看
                        console.log(obj.data);
                        window.location.href = '/index/showBuilding/schoolpart_id/'+obj['data']['schoolpart_id']+'/college_id/'+obj['data']['college_id'];
                    } else if(layEvent === 'del'){ //删除
                        layer.confirm('真的删除行么', function(index){
                            obj.del(); //删除对应行（tr）的DOM结构
                            layer.close(index);
                            //向服务端发送删除指令
                        });
                    } else if(layEvent === 'edit'){ //编辑
                        //do something
                        //同步更新缓存对应的值
                        obj.update({
                            username: '123'
                            ,title: 'xxx'
                        });
                    }
                });
            });
            college.getDataFormRemote();
        });
        var schoolPart = new WkkyData('/index/ViewSchoolPartPower',{
            credentials: 'include',
            method: "POST"
        },{schoolpart_id:schoolpart_id});
        schoolPart.setOnSuccess(function (handleResponse) {
            // 获取 CSV 数据并初始化图表
            var data = handleResponse.getData();
            var _num = [0,0,0,0,0,0,0,0,0,0,0,0];
            var max = 0;
            var title = undefined;
            $.each(data,function (index,item) {
                title = item['text_description'];
                _index = parseInt(item['month'])-1;
                if(_index>max)
                    max=_index;
                _num[_index] = parseInt(parseInt(item['num'])/1000);
            });
            var tp = _num[max++];
            for(;max < 12;max++){
                _num[max] = tp;
            }
            var chart = new Highcharts.Chart('chart', {
                title: {
                    text: title,
                    x: -20
                },
                chart: {
                    type: 'line'
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true          // 开启数据标签
                        },
                        enableMouseTracking: false // 关闭鼠标跟踪，对应的提示框、点击事件会失效
                    }
                },
                subtitle: {
                    text: '数据来源: WorldClimate.com',
                    x: -20
                },
                xAxis: {
                    categories: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
                },
                yAxis: {
                    title: {
                        text: '用电量 ( 度 )'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    valueSuffix: ' 度'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                series: [{
                    name: '配额',
                    data: _num
                }]
            });
        });
        schoolPart.getDataFormRemote();
    });
});

/*
*/
