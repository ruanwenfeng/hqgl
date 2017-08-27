<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" href="__PUBLIC__/css/base.css">
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    {block name="css"}{/block}
</head>
<body style="padding:10px;margin: 0;box-sizing: border-box;width: 100%;height: 100%">
    {block name="page-body"}
    {/block}
<script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>
<script type="text/html" id="my-bar-1">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="detail">查看</a>
</script>
<script type="text/html" id="my-bar-2">
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">删除</a>
</script>
<script type="text/html" id="my-bar-3">
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">删除</a>
</script>
    {block name="script"}
    {/block}
</body>
</html>