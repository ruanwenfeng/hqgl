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
    <input type="hidden" name="type" value="1">
    <input type="hidden" name="mode" value="2">
    <div class="layui-form-item">
        <label class="layui-form-label">校区</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="" value="{$record.schoolpart}"  placeholder="校区" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">学院</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="" value="{$record.college}"  placeholder="学院" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">楼栋</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="" value="{$record.building}" placeholder="楼栋" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">房间</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="" value="{$record.room}" placeholder="房间" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">设备描述</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="text_description" value="{$record.text_description}" placeholder="" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">设备名称</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="name" value="{$record.name}" placeholder="" autocomplete="off" class="info-input layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">品牌名称</label>
        <div style="width: 350px;" class="layui-input-inline">
            <input readonly type="text" name="brand" value="{$record.brand}"  placeholder="" autocomplete="off" class="info-input layui-input">
        </div>


    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">功率</label>
        <div style="width: 80px;" class="layui-input-inline">
            <input readonly type="text" name="power" value="{$record.power}"  placeholder="" autocomplete="off" class="info-input layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">功率单位（W）<span style="color: darkred"></span></div>

        <label class="layui-form-label" style="width: 30px;">数量</label>
        <div style="width: 80px;" class="layui-input-inline">
            <input  readonly type="text" name="number" value="{$record.number}" placeholder="" autocomplete="off" class="layui-input">
        </div>

        <label class="layui-form-label" style="width: 60px;">每日时长</label>
        <div style="width: 80px;" class="layui-input-inline">
            <input readonly type="text" name="day_time" value="{$record.day_time}" placeholder="" autocomplete="off" class="info-input layui-input">
        </div>

    </div>

    <div class="layui-form-item layui-form-text" style="padding-right: 80px;">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea readonly name="comment"  placeholder="请输入内容" class="layui-textarea">{$record.comment}</textarea>
        </div>
    </div>
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