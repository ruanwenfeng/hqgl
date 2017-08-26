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
    <input name="usergroup_id" value="{$usergroup_id}" type="hidden" />
    <div class="layui-form-item">
        <label class="layui-form-label">用户组名称</label>
        <div class="layui-input-block">
            <input type="text" name="text_description" required value="{$text_description}" lay-verify="required" placeholder="请输入用户组名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    {volist name="authorization" id="item"}
        <fieldset class="layui-elem-field">
            <legend>{$item['title']}</legend>
            <div class="layui-field-box">
                {volist name="item['college']" id="_item"}
                    <input type="hidden" name="{$_item['schoolpart_id']}[]" value="{$_item['college_id']}" />
                    {eq name="_item['flag']" value="true"}
                        <input checked type="checkbox" name="{$_item['college_id']}" title="{$_item['text_description']}">
                    {else/}
                        <input type="checkbox" name="{$_item['college_id']}" title="{$_item['text_description']}">
                    {/eq}
                {/volist}
            </div>
        </fieldset>
    {/volist}
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
                        if(document.body.clientHeight<document.documentElement.clientHeight)
                            window.parent.iframeAuto();
                        window.formSubmit = function (update) {
                            var formElement = document.querySelector("form");
                            var formData = new window.FormData(formElement);
                            var text_description =$.trim(formData.get('text_description'));
                            if(text_description==''){
                                $('input[name=text_description]').focus();
                                parent.layer.msg('用户组名称不能为空',function () {
                                    
                                });
                                return;
                            }
                            formData.set('text_description',text_description);
                            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
                            var save = new WkkyData();
                            var request = new Request('/index/saveAuthorization',{
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
                                parent.layer.msg('保存成功');
                                parent.layer.close(index); //再执行关闭
                            });
                            save.setOnFail(function (e) {
                                parent.layer.msg('保存失败',function () {
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