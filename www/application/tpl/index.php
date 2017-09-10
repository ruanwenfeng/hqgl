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
        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:void(0);">
                    <img src="__PUBLIC__/head.jpg" class="layui-nav-img">
                    {$user.user_name}
                </a>
            </li>
            <li class="layui-nav-item"><a href="/base/logout">退了</a></li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <ul class="layui-nav layui-nav-tree" >
                <li class="layui-nav-item">
                    <a href="javascript:void(0);">用电情况</a>
                    <dl class="layui-nav-child">
                        {volist name="school_part" id="row"}
                            {if condition="$row.schoolpart_id eq $schoolpart_id"}
                                <dd class="layui-this schoolpart_active"><a  data-id="{$row.schoolpart_id}" href="javascript:void(0);" data-href="/index/showCollege/schoolpart_id/{$row.schoolpart_id}">{$row.text_description}</a></dd>
                            {else /}
                                <dd><a data-id="{$row.schoolpart_id}" href="javascript:void(0);" data-href="/index/showCollege/schoolpart_id/{$row.schoolpart_id}">{$row.text_description}</a></dd>
                            {/if}
                        {/volist}
                    </dl>
                </li>
                {if condition="$admin eq true"}
                    <li class="layui-nav-item">
                        <a href="javascript:void(0);">权限系统</a>
                        <dl class="layui-nav-child">
                            <dd class="ShowChildUser"><a  href="javascript:void(0);" data-href="/index/ShowChildUser">授权管理</a></dd>
                            <dd class="ShowUserGroup"><a  href="javascript:void(0);" data-href="/index/ShowUserGroup">用户分组</a></dd>
                        </dl>
                    </li>
                {/if}
                <li class="layui-nav-item">
                    <a href="javascript:void(0);">报修系统</a>
                    <dl class="layui-nav-child">
                        <dd class=""><a  href="javascript:void(0);" data-href="/index/showPersonRepair">显示</a></dd>
                        {if condition="$admin eq false"}
                            <dd class=""><a  href="javascript:void(0);" data-href="/index/faultRepair">报修</a></dd>
                        {/if}
                    </dl>
                </li>
                <li class="count layui-nav-item"><a href="javascript:void(0);" data-href="/index/countEquipMentView">设备统计</a></li>
                <li class="compute layui-nav-item"><a href="javascript:void(0);">计算电量</a></li>
                <li class="setting layui-nav-item"><a href="javascript:void(0);">系统设置</a></li>
                <li  class="layui-nav-item"><a data-href="/index/editPassView" href="javascript:void(0);">修改密码</a></li>
            </ul>
        </div>
    </div>

    <div class="layui-body" style="box-sizing: border-box;margin: 0;padding: 0;overflow: hidden">
        {block name="page-body"}

        {/block}
    </div>
    <div class="layui-footer">
        © layui.com - 底部固定区域
    </div>
</div>
<script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>
<script type="text/html" id="actionBar">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="detail">查看</a>
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">删除</a>
</script>
<script type="text/html" id="my-bar">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="detail">查看</a>
    <a class="layui-btn layui-btn-mini" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">删除</a>
</script>
{block name="script"}{/block}
</body>
</html>