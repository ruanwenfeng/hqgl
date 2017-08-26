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
        var usergroup_input = $('.usergroup-text_description');
        $('.ShowUserGroup').addClass('layui-this').closest('.layui-nav-item').addClass('layui-nav-itemed');
        layui.use('element', function(){
            var element = layui.element;
        });
        layui.use(['element','table','layer','laytpl'], function(){
            var element = layui.element,
                table = layui.table,
                laytpl = layui.laytpl,
                form = layui.form,layer = layui.layer;
            loading();
            var userGroup = new WkkyData();
            userGroup.setRequest(new Request('/index/queryUserGroup',{
                credentials: 'include',
                method: "POST"
            }));
            userGroup.setOnSuccess(function (handleResponse) {
                table.render({
                    elem: '#usergroup-table'
                    ,cols:  [[ //标题栏
                        {field: 'index', title: '编号', align: 'center',width: 80}
                        ,{field: 'usergroup_id', title: '用户组ID', width: 320}
                        ,{field: 'text_description', title: '用户组', sort: true, width: 300}
                        ,{title:'操作',width: 160,align: 'center',templet:'#my-bar-2'}
                    ]], //设置表头
                    done:function (res, curr, count) {

                    },
                    data:$.each(handleResponse.getData(),function (index, item) {
                        item['index'] = index+1;
                    }),
                    even:true
                });
                table.on('tool', function(obj){
                    //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
                    var data = obj.data; //获得当前行数据
                    console.log(data);
                    var layEvent = obj.event; //获得 lay-event 对应的值
                    var tr = obj.tr; //获得当前行 tr 的DOM对象
                    if(layEvent === 'detail'){ //查看
                    } else if(layEvent === 'del'){ //删除
                        layer.confirm('真的删除行么', function(index){
                            var formData = new FormData();
                            formData.append('usergroup_id',data['usergroup_id']);
                            deleteGroup.setRequest(new Request('/index/deleteUserGroup',{
                                credentials: 'include',
                                method: "POST",
                                body:formData
                            }));
                            deleteGroup.getDataFormRemote();
                        });
                    } else if(layEvent === 'edit'){ //编辑
                        var index = layer.open({
                            area: ['100%','100%'],
                            title:'用户组详细详细',
                            type: 2
                            ,offset: 'auto' //具体配置参考：http://www.layui.com/doc/modules/layer.html#offset
                            ,id: 'layerDemo' //防止重复弹出
                            ,content:'/index/authorizationView/usergroup_id/'+data['usergroup_id']
                            ,btnAlign: 'c' //按钮居中
                            ,shade: 0.5 //不显示遮罩
                            ,btn: ['保存', '取消'],
                            btn1:function (e) {
                                var win = $('iframe')[0].contentWindow;
                                win.formSubmit(function () {
                                    userGroup.setRequest(new Request('/index/queryUserGroup',{
                                        credentials: 'include',
                                        method: "POST"
                                    }));
                                    userGroup.getDataFormRemote();
                                });
                            }
                        });
                        // window.iframeAuto = function () {
                        //     layer.iframeAuto(index);
                        // };
                    }
                });
            });
            userGroup.setOnAfter(function () {
                closeLoad(1);
            });
            userGroup.getDataFormRemote();

            var deleteGroup = new WkkyData();
            deleteGroup.setOnBefor(function (response) {
                loading(undefined,0);
            });
            deleteGroup.setOnSuccess(function (response) {
                userGroup.setRequest(new Request('/index/queryUserGroup',{
                    credentials: 'include',
                    method: "POST"
                }));
                userGroup.getDataFormRemote();
                layer.msg('删除成功');
            });
            deleteGroup.setOnFail(function (response) {
                layer.msg(response.getMessage(),function () {
                    
                });
            });
            deleteGroup.setOnError(function (response) {
                layer.msg('系统错误，请稍后再试！',function () {

                });
            });
            deleteGroup.setOnAfter(function (response) {
                closeLoad(1);
            });


            var add = $('.create-usergroup');
            var wkky = new WkkyData();
            wkky.setOnBefor(function () {
                loading();
            });
            wkky.setOnSuccess(function () {
                userGroup.setRequest(new Request('/index/queryUserGroup',{
                    credentials: 'include',
                    method: "POST"
                }));
                usergroup_input.val(null);
                userGroup.getDataFormRemote();
                layer.msg('创建用户组成功');
            });
            wkky.setOnFail(function (e) {
                layer.msg(e.getMessage(),function () {
                    
                });
            });
            wkky.setOnError(function () {
                layer.msg('系统错误，请稍后再试！');
            });
            wkky.setOnAfter(function () {
                closeLoad(1);
            });
            add.click(function () {
                var text_description = usergroup_input.val();
                if($.trim(text_description)==''){
                    layer.msg('用户组名称不能为空',function () {

                    });
                    return;
                }
                var formData = new window.FormData();
                formData.append('text_description',text_description);
                var request = new Request('/index/createUserGroup',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                });
                wkky.setRequest(request);
                wkky.getDataFormRemote();
            });
            usergroup_input.keydown(function (e) {
                if(e.keyCode==13)
                    add.click();
            });
        });
    });
});