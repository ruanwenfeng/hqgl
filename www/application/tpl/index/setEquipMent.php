{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}

<div class="main-div-body" >
    <form class="layui-form">
        <div class="layui-inline"  title="留空则代表所有">
            <select lay-filter="equipmentType"  name="equipmentType" title="equipmentType" lay-search>
                <option selected="selected" value="">请选择一个设备种类</option>
                {volist name="equipType" id="row"}
                    <option value="{$row.text_description}">{$row.text_description}</option>
                {/volist}
            </select>
        </div>
    </form>
    <table  id="data-table"></table>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['setEquipMent']);
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