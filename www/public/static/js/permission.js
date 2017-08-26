/**
 * Created by Administrator on 2017/8/24.
 */
window.require(['jquery','layui'],function ($) {
    layui.config({
        dir: '/static/layui/'
    });

    $(function () {
        window.load = null;
        window.flag = 0;
        $('.ShowChildUser').addClass('layui-this').closest('.layui-nav-item').addClass('layui-nav-itemed');

        layui.use(['element','table','layer','laytpl'], function(){
            var element = layui.element,
                table = layui.table,
                layer = layui.layer,
                laytpl = layui.laytpl,
                form = layui.form;
            loading();
            var child = new WkkyData('/index/queryChildUser',{
                credentials: 'include',
                method: "POST"
            },{

            });
            child.setOnSuccess(function (handleResponse) {
                console.log(handleResponse);
                table.render({
                    elem: '#child-user-table'
                    ,cols:  [[ //标题栏
                        // {checkbox: true,width:200}
                        {field: 'index', title: '编号', align: 'center',width: 80}
                        ,{field: 'user_id', title: '用户ID', width: 150}
                        ,{field: 'user_name', title: '用户名', width: 150}
                        ,{field: 'pass', title: '密码', width: 320}
                        ,{field: 'text_description', title: '用户组', width: 150}
                        // ,{field: 'authorization', title: '权限描述',templet:'#authorization',width:100, align:'center',}
                        ,{title:'操作',width: 160,align: 'center',templet:'#my-bar-3'}
                    ]], //设置表头
                    done:function (res, curr, count) {

                    },
                    data:$.each(handleResponse.getData(),function (index, item) {
                        item['index'] = index+1;
                    }),
                    even:true
                });
                table.on('tool', function(obj){
                    console.log(obj.data);
                    //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data; //获得当前行数据
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象
                    if(layEvent === 'edit'){ //编辑
                        var index = layer.open({
                            area:['auto','400px'],
                            title:'修改用户所属组',
                            type: 2
                            ,offset: 'auto' //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
                            ,id: 'layerDemo' //防止重复弹出
                            ,content:'/index/updateUserPermissView/usergroup_id/'+data['usergroup_id']+'/user_id/'+data['user_id']
                            ,btnAlign: 'c' //按钮居中
                            ,shade: 0.5 //不显示遮罩
                            ,btn: ['保存', '取消'],
                            btn1:function (e) {
                                var win = $('iframe')[0].contentWindow;
                                win.formSubmit(function () {
                                    child.setRequest('/index/queryChildUser',{
                                        credentials: 'include',
                                        method: "POST"
                                    });
                                    child.getDataFormRemote();
                                });
                            }
                        });
                    }
                });
            });
            child.setOnAfter(function () {
                closeLoad(1);
            });
            child.getDataFormRemote();
        });



    });
});