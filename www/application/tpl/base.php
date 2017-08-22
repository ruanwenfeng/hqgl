<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" href="__PUBLIC__/css/base.css">
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    {block name="css"}{/block}
</head>
<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header">
        <div class="layui-logo">用电设备管理系统</div>
        <ul class="layui-nav layui-layout-left">
            <li class="layui-nav-item"><a href="">控制台</a></li>
            <li class="layui-nav-item"><a href="">商品管理</a></li>
            <li class="layui-nav-item"><a href="">用户</a></li>
            <li class="layui-nav-item">
                <a href="javascript:void(0);">其它系统</a>
                <dl class="layui-nav-child">
                    <dd><a href="">邮件管理</a></dd>
                    <dd><a href="">消息管理</a></dd>
                    <dd><a href="">授权管理</a></dd>
                </dl>
            </li>
        </ul>
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:void(0);">
                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">
                    贤心
                </a>
                <dl class="layui-nav-child">
                    <dd><a href="">基本资料</a></dd>
                    <dd><a href="">安全设置</a></dd>
                </dl>
            </li>
            <li class="layui-nav-item"><a href="">退了</a></li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree" >
                <li class="layui-nav-item layui-nav-itemed">
                    <a href="javascript:void(0);">校区管理</a>
                    <dl class="layui-nav-child">
                        {volist name="school_part" id="row"}
                            {if condition="$row.schoolpart_id eq $schoolpart_id"}
                                <dd class="layui-this"><a  data-id="{$row.schoolpart_id}" href="/index/showCollege/schoolpart_id/{$row.schoolpart_id}">{$row.text_description}</a></dd>
                            {else /}
                                <dd><a data-id="{$row.schoolpart_id}" href="/index/showCollege/schoolpart_id/{$row.schoolpart_id}">{$row.text_description}</a></dd>
                            {/if}
                        {/volist}
                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:void(0);">解决方案</a>
                    <dl class="layui-nav-child">
                        <dd><a href="">移动模块</a></dd>
                        <dd><a href="">后台模版</a></dd>
                        <dd><a href="">电商平台</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item"><a href="">产品</a></li>
                <li class="layui-nav-item"><a href="">大数据</a></li>
            </ul>
        </div>
    </div>

    <div class="layui-body">
        <div style="padding-top: 15px;">
            {block name="page-body"}{/block}
        </div>
    </div>

    <div class="layui-footer">
        © layui.com - 底部固定区域
    </div>
</div>
<script type="text/html" id="actionBar">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="detail">查看</a>
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">删除</a>
</script>
<script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>

{block name="script"}{/block}
</body>
</html>