/**
 * Created by Administrator on 2017/9/9.
 */
window.require(['jquery','layui','cookie'],function ($) {
    layui.config({
        dir: '/static/layui/'
    });
    window.load = null;
    window.flag = 0;
    window.mode = 1;
    window.schoolepart_id = '';
    window.college_id = '';
    window.equiptype = '';
    layui.use(['element','layer','form','table'], function(){
        var element = layui.element,
            table = layui.table,
            layer = layui.layer,form = layui.form;
        var where = {text_description:''};
        countSchoolPart(where);
        form.on('select(equipmentType)', function(data){
            where['text_description'] = data.value;
            countSchoolPart(where);
        });
        var equip = new window.WkkyData();

        equip.setOnBefor(function () {
           loading();
        });

        equip.setOnFail(function (response) {
            layer.msg(response.getMessage());
        });
        equip.setOnError(function () {
            layer.msg('系统异常');
        });
        equip.setOnAfter(function () {
           closeLoad(1);
        });
        function countSchoolPart(where) {
            loading();
            table.render({
                elem: '#data-table'
                ,cols:  [[ //标题栏
                    {field: 'index', title: '编号', align: 'center',width: 80}
                    ,{field: 'text_description', title: '设备类型', sort: true, width: 250}
                    ,{field: 'day_time',title:'每日时长（小时）',edit:'text',width: 250,sort: true}
                ]], //设置表头
                method:'post',
                done:function () {
                    closeLoad(1);
                },
                limit:typeof $.cookie('limit') == typeof  undefined ? 30 :$.cookie('limit'),
                page: true,
                even:true,
                where:where,
                url:'/index/setEquipDataTable',
                loading: true
            });
            table.on('edit', function(obj){ //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"

                var value =  Number(obj.value);
                if(isNaN(value) || Math.floor(value) !== value){
                    layer.msg('请输入整数');
                    return;
                }
                if(value>24){
                    layer.msg('不能大于 24');
                    return;
                }
                if(value<1){
                    layer.msg('不能小于 1');
                    return;
                }
                var formData = new window.FormData();
                formData.append('text_description',obj.data['text_description']);
                formData.append('day_time',value);
                equip.setRequest(new Request('/index/updateEquipDayTime',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                }));
                equip._success = [];
                equip.setOnSuccess(function () {
                    obj.data[obj.field] =parseInt(value); //更新缓存中的值
                    layer.msg('修改成功');
                });
                equip.getDataFormRemote();
            });
        }
    });
});
