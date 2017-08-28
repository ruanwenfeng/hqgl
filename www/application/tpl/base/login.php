<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>登录</title>
    <link rel="stylesheet" href="__PUBLIC__/css/base.css">
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    <script type="text/javascript" src="__PUBLIC__/layui/layui.js"></script>
</head>
<body>
<div class="login_page">
    <img class="logo-login" src="__PUBLIC__/logo-login.png" alt="logo">
    <h1>欢迎使用 Lz</h1>
    <form class="layui-form">
        <div class="layui-form-item">
            <div class="layui-input-inline input-custom-width">
                <input type="text" name="username"  placeholder="用户名" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-inline input-custom-width">
                <input type="password" name="password"  placeholder="密码" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <div class=" input-custom-width" style="position: relative">
                <input type="text" name="captcha"  placeholder="验证码" autocomplete="off" class="layui-input">
                <div class="captcha"><img src="/base/captcha.html" alt="captche" title='点击切换' onclick="this.src='/base/captcha?id='+Math.random()"></div>
            </div>
        </div>
        <div class="layui-form-item">
            <div class="layui-input-inline input-custom-width">
                <button class="layui-btn input-custom-width" lay-submit="" lay-filter="login">立即登陆</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    layui.use('form',function(){
        var form = layui.form
            ,jq = layui.jquery;

        //监听提交
        form.on('submit(login)', function(data){
            loading = layer.load(2, {
                shade: [0.2,'#000'] //0.2透明度的白色背景
            });
            var param = data.field;
            jq.post('/base/checklogin.html',param,function(data){
                if(data.status == 1){
                    layer.close(loading);
                    layer.msg('登录成功', {icon: 1, time: 1000}, function(){
                        location.href = '/index'
                    });
                }else{
                    layer.close(loading);
                    layer.msg(data.message,function () {
                    });
                    jq('.captcha img').attr('src','/base/captcha?id='+Math.random());
                }
            });
            return false;
        });
    });
</script>
</body>
</html>