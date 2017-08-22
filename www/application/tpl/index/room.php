{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}

<div class="layui-container main-div-body"
     data-schoolpart-id="{$schoolpart_id}"
     data-college-id="{$college_id}"
     data-building-id="{$building_id}">
    <span class="layui-breadcrumb">
      <a href="">{$schoolpart_text}</a>
      <a href="">{$college_text}</a>
      <a href="javascript:void(0)"><cite>{$building_text}</cite></a>
      <a href="javascript:void(0)"><cite>房间</cite></a>
    </span>
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