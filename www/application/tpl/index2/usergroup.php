{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}

<div class="layui-container main-div-body" >

    <div class="layui-inline">
        <input type="text" name="text_description"   placeholder="请输入用户组名称" autocomplete="off" class="usergroup-text_description layui-input">
    </div>
    <button  class="create-usergroup layui-btn">
        <i class="layui-icon">&#xe608;</i> 添加
    </button>
    <table  id="usergroup-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['usergroup']);
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
{/block}