/**
 * Created by Administrator on 2017/8/21.
 */
//注意：导航 依赖 element 模块，否则无法进行功能性操作
window.require(['jquery','layui'],function ($) {
    layui.config({
        dir: '/static/layui/'
    });
    layui.use('element', function(){
        var element = layui.element;
    });
});
