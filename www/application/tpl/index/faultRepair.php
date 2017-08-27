{extend name="base" /}
{block name="css"}
{/block}
{block name="page-body"}

<div id="lucasRepairMain">
    <div class="layui-row" style="margin-top: 30px">
    <form class="layui-form" action="">
        <div class="layui-form-item">
            <div>
            <span style="float: left;line-height: 38px;margin-left: 10px;margin-right: 20px;">请选择您所在的地址</span>
            </div>
            <div class="layui-input-inline">
                <select name="quiz1" class="layui-form" id="schoolSelect" lay-filter="schoolSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz2" class="layui-form" id="collegeSelect" lay-filter="collegeSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz3" class="layui-form" id="buildingSelect" lay-filter="buildingSelectFilter">
                </select>
            </div>
            <div class="layui-input-inline">
                <select name="quiz3" class="layui-form" id="roomSelect" lay-filter="roomSelectFilter">
                </select>
            </div>
        </div>
    </form>
    </div>
</div>
<table  id="equipmentTable"></table>
<div id="extraDiv">
    <button class="layui-btn layui-btn-normal" id="confirmRepair">确定</button>
</div>

{/block}
{block name="script"}
<script>
    require(['faultRepair']);
</script>
{/block}