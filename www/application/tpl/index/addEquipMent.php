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
        <div class="layui-inline div-equipment" title="留空则在下面填写">
            <select disabled lay-filter="equipmentType" class="equipmentType" name="equipmentType" title="equipmentType" lay-search>
                <option selected="selected" value="">请选择设备种类</option>
                {volist name="equipType" id="row"}
                    <option value="{$row.text_description}-{$row.power}">{$row.text_description} {$row.power}</option>
                {/volist}
            </select>
        </div>
        <br>
        <br>
        <div class="layui-form-item">
            <label class="layui-form-label">设备描述</label>
            <div style="width: 350px;" class="layui-input-inline">
                <input type="text" name="text_description"  placeholder="请输入设备描述" autocomplete="off" class="info-input layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">可以从上方选择已存在的设备<span style="color: darkred">（必填）</span></div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">设备名称</label>
            <div style="width: 350px;" class="layui-input-inline">
                <input type="text" name="name"  placeholder="请输入设备名称" autocomplete="off" class="info-input layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">（选填）</div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">品牌名称</label>
            <div style="width: 350px;" class="layui-input-inline">
                <input type="text" name="brand"  placeholder="请输入品牌名称" autocomplete="off" class="info-input layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">（选填）</div>


        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">功率</label>
            <div style="width: 80px;" class="layui-input-inline">
                <input type="number" name="power"  placeholder="" autocomplete="off" class="info-input layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">功率单位（W）<span style="color: darkred">（必填）</span></div>

            <label class="layui-form-label" style="width: 30px;">数量</label>
            <div style="width: 80px;" class="layui-input-inline">
                <input type="number" name="number"  placeholder="" autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux"><span style="color: darkred">（必填）</span></div>

            <label class="layui-form-label" style="width: 60px;">每日时长</label>
            <div style="width: 80px;" class="layui-input-inline">
                <input type="number" name="day_time"  placeholder="" autocomplete="off" class="info-input layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">用于电量计算<span style="color: darkred">（必填）</span></div>

        </div>

        <div class="layui-form-item layui-form-text" style="padding-right: 80px;">
            <label class="layui-form-label">备注</label>
            <div class="layui-input-block">
                <textarea name="comment" placeholder="请输入内容" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item" >
            <div class="layui-input-block">
                <button class="layui-btn" lay-submit lay-filter="add">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary">重置</button>
            </div>
        </div>

    </form>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['addEquipMent']);
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