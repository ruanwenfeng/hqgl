{extend name="base" /}
{block name="css"}
{/block}
{block name="page-body"}
<div class="" style="overflow: auto">
    {if condition="($flag == 1)"}
        {volist name="data" id="vo"}
            <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
                <div class="layui-field-box">
                    <div class="showPostData">
                        <div class="left-info" style="float: left;width: 300px;margin-left: 100px;text-align: left;margin-top: 10px">
                            <span class="descript" values="{$vo.text_description}">报修的物品： {$vo.text_description}</span><br/>
                            <span class="amount" values="{$vo.amount}">报修的数量： {$vo.amount}</span><br/>
                            <span class="school" keys="{$vo.scholDescript}" values="{$vo.scholId}">所在的校区： {$vo.scholDescript}</span><br/>
                            <span class="college" keys="{$vo.collegeDescript}" values="{$vo.collegeId}">所在的学院： {$vo.collegeDescript}</span><br/>
                            <span class="building" keys="{$vo.buildingDescript}" values="{$vo.buildingId}">所在的楼栋： {$vo.buildingDescript} </span><br/>
                            <span class="room" keys="{$vo.roomDescript}" values="{$vo.roomId}">所在的房间： {$vo.roomDescript}</span>
                        </div>
                        <div class="right-reason" style="float: right;width: 600px;text-align: center;margin-right: 100px">
                            <fieldset class="layui-elem-field">
                                <legend>申报理由</legend>
                                <div class="layui-field-box">
                                    <textarea class="text-reason"  style="width: 550px;height: 110px;"></textarea>
                                </div>
                            </fieldset>

                        </div>
                    </div>
                </div>
            </fieldset>
        {/volist}
    {/if}
    <div class="layui-row">
        <button class="layui-btn layui-btn-normal" values="{$flag}" id="lucasSubmit" style="float: right;margin-right: 100px">确定报修</button>
    </div>
</div>
{/block}
{block name="script"}
<script>
    require(['showReason']);

</script>
{/block}