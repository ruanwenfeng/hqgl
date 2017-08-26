{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class=" main-div-body"
     data-schoolpart-id="{$schoolpart_id}"
     data-college-id="{$college_id}"
     data-building-id="{$building_id}">
    <span class="layui-breadcrumb">
        <a href="/index/showCollege/schoolpart_id/{$schoolpart_id}">{$schoolpart_text}</a>
        <a href="/index/showBuilding/schoolpart_id/{$schoolpart_id}/college_id/{$college_id}">{$college_text}</a>
        <a href="javascript:void(0)"><cite>{$building_text}</cite></a>
        <a href="javascript:void(0)"><cite>房间</cite></a>
    </span>
    <div class="line"></div>
    <div id="chart" style="min-width:400px;height:300px"></div>
    <table  id="room-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['room']);
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