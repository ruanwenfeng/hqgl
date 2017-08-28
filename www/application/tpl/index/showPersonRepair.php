{extend name="base" /}
{block name="css"}
<style>
    .lucasDis{
    pointer-events: none;
    background-color: red;
    }

</style>

{/block}
{block name="page-body"}
<div>
    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief">
        <ul class="layui-tab-title">
            <li class="layui-this">新消息</li>
            <li>待处理</li>
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
            <div class="layui-tab-item">
                <div class="layui-row">
                    <table  id="dealTable"></table>
                </div>
                <div class="layui-row">
                    <div  id="lucas-history"></div>
                </div>
            </div>
            <div class="layui-tab-item">
                <div class="layui-row">
                    <table  id="realHistoryTable"></table>
                </div>
                <div class="layui-row">
                    <div  id="realHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}

{if condition="$isAdmin eq 'shi'"}
<script type="text/html" id="barNewRecord" lay-filter="newRecord">
        <a class="layui-btn layui-btn-mini" lay-event="detail">查看</a>
        <a  class="layui-btn layui-btn-mini" lay-event="pass">审核通过</a>
        <a class="layui-btn layui-btn-danger layui-btn-mini" lay-event="noPass">拒绝申请</a>
</script>
{else /}
<script type="text/html" id="barNewRecord" lay-filter="newRecord">
    <a class="layui-btn layui-btn-mini" lay-event="detail">查看</a>
</script>
{/if}

{if condition="$isAdmin eq 'shi'"}
<script type="text/html" id="barDeal" lay-filter="deal">
    <a class="layui-btn layui-btn-mini" lay-event="continueUse">继续使用</a>
</script>
{else /}
<script type="text/html" id="barDeal" lay-filter="deal">
    <a style="pointer-events:none;" class="layui-btn layui-btn-mini" lay-event="continueUse">等待结果</a>
</script>
{/if}

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
    require(['showPersonRepair','historyRecord','realHistory']);
</script>
{/block}