/**
 * Created by Administrator on 2017/8/21.
 */
window.require(['jquery','layui'],function ($) {
    $(function () {
        layui.config({
            dir: '/static/layui/'
        });
        layui.use(['element','table'], function(){
            var element = layui.element,table = layui.table;
            var schoolpart_id = $('.main-div-body').attr('data-schoolpart-id');
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
            });
            college.getDataFormRemote();
        });


    });
});