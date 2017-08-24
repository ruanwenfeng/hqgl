<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"../application/tpl/index/faultRepair.php";i:1503504408;s:27:"../application/tpl/base.php";i:1503403815;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" href="__PUBLIC__/css/base.css">
    <link rel="stylesheet" href="__PUBLIC__/layui/css/layui.css">
    
<style>
    /*#lucasRepairMain{*/
        /*display: block;*/
    /*}*/
    /*#lucasRepairMain table{*/
        /*text-align: center;*/
    /*}*/
    /*#lucasRepairMain .confirmRepair{*/
        /*display: none;*/
    /*}*/
</style>

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
                        <?php if(is_array($school_part) || $school_part instanceof \think\Collection || $school_part instanceof \think\Paginator): $i = 0; $__LIST__ = $school_part;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;if($row['schoolpart_id'] == $schoolpart_id): ?>
                                <dd class="layui-this"><a  data-id="<?php echo $row['schoolpart_id']; ?>" href="/index/showCollege/schoolpart_id/<?php echo $row['schoolpart_id']; ?>"><?php echo $row['text_description']; ?></a></dd>
                            <?php else: ?>
                                <dd><a data-id="<?php echo $row['schoolpart_id']; ?>" href="/index/showCollege/schoolpart_id/<?php echo $row['schoolpart_id']; ?>"><?php echo $row['text_description']; ?></a></dd>
                            <?php endif; endforeach; endif; else: echo "" ;endif; ?>
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
            
<div id="lucasRepairMain">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <label class="layui-form-label">请选择</label>
            <div class="layui-input-inline">
                <select name="quiz1" class="layui-form" id="schoolSelect" lay-filter="schoolSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz2" class="layui-form" id="collegeSelect" lay-filter="collegeSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz3" class="layui-form" id="buildingSelect" lay-filter="buildingSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz3" class="layui-form" id="roomSelect" lay-filter="roomSelectFilter">
                </select>
            </div>
        </div>

    </form>
</div>
<table  id="equipmentTable"></table>
<div id="extraDiv">
    <button class="layui-btn layui-btn-normal" id="confirmRepair">确定</button>
</div>


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


<script>
    require(['faultRepair']);
</script>

</body>
</html>