/**
 * Created by Administrator on 2017/8/21.
 */
//注意：导航 依赖 element 模块，否则无法进行功能性操作
window.require(['jquery','layui'],function ($) {
    layui.config({
        dir: '/static/layui/'
    });
    window.load = null;
    window.flag = 0;
    layui.use(['element','layer'], function(){
        var element = layui.element,
            layer = layui.layer;

        element.on('nav', function(elem){
            if($(elem).hasClass('compute')){
                var power = new window.WkkyData('/index/savePower',{
                    credentials: 'include',
                    method: "POST"
                });
                power.setOnBefor(function () {
                    loading();
                });
                power.setOnSuccess(function (response) {
                    layer.msg('计算成功');
                });
                power.setOnFail(function (response) {
                    layer.msg(response.getMessage());
                });
                power.setOnError(function () {
                    layer.msg('计算成功');
                });
                power.setOnAfter(function () {
                   closeLoad(1);
                });
                power.getDataFormRemote();
            }else {
                $('iframe').attr('src',$(elem).find('a').attr('data-href'));
            }
        });
    });
});
