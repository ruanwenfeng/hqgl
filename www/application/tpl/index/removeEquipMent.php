{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="main-div-body"">
<form class="layui-form">

    <input type="hidden" name="type" value="1">
    <input type="hidden" name="mode" value="2">
    <div class="layui-inline" >
        <select name="schoolpart_id" lay-filter="schoolpart" title="schoolpart" lay-search>
            <option selected="selected" value="">请选择校区</option>
            {volist name="school_part" id="row"}
            <option value="{$row.schoolpart_id}">{$row.text_description}</option>
            {/volist}
        </select>
    </div>
    <div  class="layui-inline div-college" >
        <select lay-filter="college" class="college" name="college_id"  title="college" lay-search>
            <option value="">请选择学院</option>
        </select>
    </div>
    <div  class="layui-inline div-building" >
        <select lay-filter="building" class="building" name="building_id"  title="building" lay-search>
            <option value="">请选择楼栋</option>
        </select>
    </div>
    <div  class="layui-inline div-room" >
        <select lay-filter="room" class="room" name="room_id"  title="room" lay-search>
            <option value="">请选择房间</option>
        </select>
    </div>

</form>
<table  id="data-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/html" id="my-1">
    <a class="layui-btn layui-btn-primary layui-btn-mini" lay-event="remove">报废</a>
</script>
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['removeEquipMent']);
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