{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="main-div-body" >
    <form enctype="multipart/form-data" class="layui-form" >
        <div class="layui-inline">
            <input type="text" name="user_name"   placeholder="请输入用户组名" autocomplete="off" class="usergroup-text_description layui-input">
        </div>
        <div class="layui-inline">
            <input type="text" name="pass"   placeholder="请输入密码" autocomplete="off" class="usergroup-text_description layui-input">
        </div>
        <div class="layui-inline">
            <select title="usergroup" name="usergroup_id" lay-search>
                <option value="">请选择一个用户组</option>
                {volist name="usergroup" id="item"}
                    <option value="{$item['usergroup_id']}">{$item['text_description']}</option>
                {/volist}
            </select>
        </div>
        <button lay-submit="" class="create-user layui-btn">
            <i class="layui-icon">&#xe608;</i> 创建用户
        </button>
    </form>
    <table  id="child-user-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['permission']);
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
<script type="text/html" id="authorization">
    <a class="layui-btn layui-btn-mini" lay-event="view-author">详情</a>
</script>
<div style="opacity: 0;" class="qqlayui-hide">
</div>
{/block}