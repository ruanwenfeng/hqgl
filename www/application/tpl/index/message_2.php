{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="main-div-body">
    <div class="layui-tab layui-tab-brief">
        <ul class="layui-tab-title">
            <li class="layui-this">未读</li>
            <li>待处理</li>
            <li>历史记录</li>
        </ul>
        <div class="layui-tab-content"></div>
    </div>
    <table  id="data-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/html" id="my-1">
    {{#  if(d.type == 1){ }}
        新增
    {{#  } else { }}
        报废
    {{#  } }}
</script>
<script type="text/html" id="my-2">
    {{#  if(d.status == 1){ }}
        未读
    {{#  } else if(d.status == 2) { }}
        待处理
    {{#  } else if(d.status == 3) { }}
        同意
    {{#  } else { }}
        拒绝
    {{#  } }}
</script>
<script type="text/html" id="my-3">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="detail">详情</a>
</script>
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['message_2']);
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