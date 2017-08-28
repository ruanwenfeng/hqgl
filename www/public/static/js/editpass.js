/**
 * Created by Administrator on 2017/8/28.
 */
window.require(['jquery','layui'],function ($) {
    $(function () {
        window.load = null;
        window.flag = 0;
        layui.config({
            dir: '/static/layui/'
        });
        layui.use(['form','layer'],function () {
            var form = layui.form,layer = layui.layer;
            form.on('submit(editpass)', function(data){
                var param = data.field;
                if(param.rep_pass != param.new_pass){
                    layer.msg('密码不一致',function () {
                    });
                    $('input[name=rep_pass]').val(null);
                    return false;
                }
                loading();
                $.post('/index/editPass',param,function(data){
                    if(data.status == 1){
                        closeLoad(1);
                        layer.msg('修改成功', {icon: 1, time: 1000});
                        $('input[name=pass]').val(null);
                        $('input[name=new_pass]').val(null);
                        $('input[name=rep_pass]').val(null);
                    }else{
                        closeLoad(1);
                        layer.msg(data.message,function () {
                        });
                        $('input[name=pass]').val(null);
                    }
                });
                return false;
            });
        });
    })
});