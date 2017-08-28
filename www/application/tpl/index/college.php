{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}

<div class="main-div-body" data-schoolpart-id="{$schoolpart_id}">
    <span class="layui-breadcrumb">
        <a href="javascript:void(0)"><cite>{$schoolpart_text}</cite></a>
        <a href="javascript:void(0)"><cite>学院</cite></a>
    </span>

    <span>
        <select title="year" name="year">
            {volist name="power_year" id="item"}
                {eq name="item" value="$curr_year"}
                    <option selected  value="{$item}">{$item} 年</option>
                {else/}
                    <option  value="{$item}">{$item} 年</option>
                {/eq}
            {/volist}
        </select>
    </span>
    <div class="line"></div>
    <div id="chart" style="min-width:400px;height:300px"></div>
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