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

        layui.use(['element','table','layer','laytpl','form'], function(){
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
                    page: true,
                    even:true
                });



                var deleteUser = new WkkyData();
                deleteUser.setOnBefor(function (response) {
                    loading(undefined,0);
                });
                deleteUser.setOnSuccess(function (response) {
                    child.setRequest(new Request('/index/queryChildUser',{
                        credentials: 'include',
                        method: "POST"
                    }));
                    child.getDataFormRemote();
                    layer.msg('删除成功');
                });
                deleteUser.setOnFail(function (response) {
                    layer.msg(response.getMessage(),function () {

                    });
                });
                deleteUser.setOnError(function (response) {
                    layer.msg('系统错误，请稍后再试！',function () {

                    });
                });
                deleteUser.setOnAfter(function (response) {
                    closeLoad(1);
                });

                table.on('tool', function(obj){
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
                    else if(layEvent === 'del'){
                        layer.confirm('真的删除行么', function(){
                            var formData = new FormData();
                            formData.append('user_id',data['user_id']);
                            deleteUser.setRequest(new Request('/index/deleteUser',{
                                credentials: 'include',
                                method: "POST",
                                body:formData
                            }));
                            deleteUser.getDataFormRemote();
                        });
                    }
                });
            });
            child.setOnAfter(function () {
                closeLoad(1);
            });
            child.getDataFormRemote();

            var username_input = $('input[name=user_name]');
            var pass_input = $('input[name=pass]');
            var wkky = new WkkyData();
            wkky.setOnBefor(function () {
                loading();
            });
            wkky.setOnSuccess(function () {
                child.setRequest(new Request('/index/queryChildUser',{
                    credentials: 'include',
                    method: "POST"
                }));
                username_input.val(null);
                pass_input.val(null);
                child.getDataFormRemote();
                layer.msg('创建用户成功');
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
            form.on('submit',function (data) {
                console.log(data.field);
                var user_name = data.field['user_name'];
                var pass = data.field['pass'];
                var usergroup = data.field['usergroup_id'];
                if($.trim(user_name) == ''){
                    layer.msg('用户名不能为空',function () {

                    });
                    return false;
                }
                if($.trim(pass)==''){
                    layer.msg('用户组密码不能为空',function () {

                    });
                    return false;
                }
                if($.trim(usergroup)==''){
                    layer.msg('请选择一个用户组',function () {

                    });
                    return false;
                }
                var formData = new window.FormData();
                formData.append('user_name',user_name);
                formData.append('pass',pass);
                formData.append('usergroup_id',usergroup);
                var request = new Request('/index/createUser',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                });
                wkky.setRequest(request);
                wkky.getDataFormRemote();
                return false;
            });
        });
    });
});