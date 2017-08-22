{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="layui-container main-div-body" data-schoolpart-id="{$schoolpart_id}">
    <table  id="college-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['college']);
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