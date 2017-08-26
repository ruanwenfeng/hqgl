<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="__PUBLIC__/css/base.css">
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
</head>
<body style="background-color: white;padding:20px;height: 100%;overflow: auto">
<form enctype="multipart/form-data" class="layui-form disabled-form" >
    <input name="user_id" value="{$user_id}" type="hidden" />
    <select title="usergroup" name="usergroup_id" >
        <option value="">请选择一个用户组</option>
        {volist name="usergroup" id="item"}
            {eq name="item['usergroup_id']" value="$usergroup_id"}
                <option selected value="{$item['usergroup_id']}">{$item['text_description']}</option>
            {else/}
                <option value="{$item['usergroup_id']}">{$item['text_description']}</option>
            {/eq}
        {/volist}
    </select>
</form>
<script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>
<script type="text/javascript">
    (function () {
        var app = function () {
            window.require(['jquery','layui'],function ($) {
                $(function () {
                    layui.config({
                        dir: '/static/layui/'
                    });
                    layui.use('form', function(){
                        var form = layui.form;
                        window.formSubmit = function (update) {
                            var formElement = document.querySelector("form");
                            var formData = new window.FormData(formElement);
                            var usergroup_id =$.trim(formData.get('usergroup_id'));
                            if(usergroup_id==''){
                                parent.layer.msg('请选择一个用户组',function () {

                                });
                                return;
                            }
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            var save = new WkkyData();
                            var request = new Request('/index/updateUserPermiss',{
                                credentials: 'include',
                                method: "POST",
                                body:formData
                            });
                            save.setRequest(request);
                            var load ;
                            save.setOnBefor(function () {
                                load = parent.layer.load(2);
                            });
                            save.setOnSuccess(function () {
                                update();
                                parent.layer.msg('修改成功');
                                parent.layer.close(index); //再执行关闭
                            });
                            save.setOnFail(function (e) {
                                parent.layer.msg(e.getMessage(),function () {
                                });
                            });
                            save.setOnError(function () {
                                parent.layer.msg('系统错误，请稍后再试！',function () {
                                });
                            });
                            save.setOnAfter(function () {
                                parent.layer.close(load);
                            });
                            save.getDataFormRemote();
                        };
                    });
                });
            })
        };
        function init() {
            if(window.complete){
                app();
            }else{
                setTimeout(init,50);
            }
        }
        init();
    })();
</script>
</body>
</html>