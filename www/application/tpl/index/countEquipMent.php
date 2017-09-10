{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}

<div class="main-div-body" >
    <form class="layui-form">
        <div class="layui-inline" title="留空则代表所有">
            <select name="schoolpart" lay-filter="schoolpart" title="schoolpart" lay-search>
                <option selected="selected" value="">请选择校区</option>
                {volist name="school_part" id="row"}
                    <option value="{$row.schoolpart_id}">{$row.text_description}</option>
                {/volist}
            </select>
        </div>
        <div style="display: none;" class="layui-inline div-college" title="留空则代表所有">
            <select lay-filter="college" class="college" name="college"  title="college" lay-search>
                <option value="">请选择学院</option>
            </select>
        </div>
        <div class="layui-inline"  title="留空则代表所有">
            <select lay-filter="equipmentType"  name="equipmentType" title="equipmentType" lay-search>
                <option selected="selected" value="">请选择一个设备种类</option>
                {volist name="equipType" id="row"}
                    <option value="{$row.text_description}">{$row.text_description}</option>
                {/volist}
            </select>
        </div>
        <br>
        <input checked="checked" type="radio" name="queryType" value="1" title="校区">
        <input type="radio" name="queryType" value="0" title="学院（部门）" >
    </form>
    <table  id="data-table"></table>
    <script type="text/html" id="schoolpart_text">
        <span>asdasd</span>
    </script>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['countEquipMent']);
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