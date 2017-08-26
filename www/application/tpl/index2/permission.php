{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="layui-container main-div-body" >
    <div class="line"></div>
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