{extend name="base" /}
{block name="css"}

{/block}
{block name="page-body"}
<div>
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">新消息</li>
            <li>历史记录</li>
        </ul>
        <div class="layui-tab-content">

            <div class="layui-tab-item layui-show">
                <div class="layui-row">
                    <table  id="auditingTable"></table>
                </div>
                <div class="layui-row">
                    <div  id="lucas-paging"></div>
                </div>
            </div>
            <div class="layui-tab-item">内容2</div>
        </div>
    </div>
</div>
{/block}
{block name="script"}

<script type="text/html" id="barDemo">
        <a class="layui-btn layui-btn-mini" lay-event="detail">查看</a>
        <a class="layui-btn layui-btn-mini" lay-event="edit">审核通过</a>
        <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="del">拒绝申请</a>
</script>

<script id="applyContent" type="text/html">
        {{#  layui.each(d['dataRepair'], function(index, item){ }}
            <h4>报修的物品：{{item['text_description']}}</h4><br/>
            <h3>报修的数量：{{item['amount']}}</h3><br/>
        {{#  }); }}
        {{#  if(d.length === 0){ }}
            无数据
        {{#  } }}
</script>
<script>
    require(['showPersonRepair']);
</script>
{/block}