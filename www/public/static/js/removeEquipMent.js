/**
 * Created by Administrator on 2017/9/14.
 */
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
    window.school_text = '';

    window.college_id = '';
    window.college_text = '';

    window.building_id = '';
    window.building_text = '';

    window.room_id = '';
    window.building_text = '';

    window.where = {};
    layui.use(['element','layer','form','table'], function(){
        var element = layui.element,
            table = layui.table,
            layer = layui.layer,form = layui.form;
        var college = new WkkyData();
        college.setOnAfter(befor);
        college.setOnSuccess(function (response) {
            var data = response.getData();
            $('.college').html(null).append('<option value="">请选择学院</option>');
            $.each(data,function (index, item) {
                $('.college').append($('<option value="'+item['college_id']+'">'+(item['text_description']==''?'(暂无名称)':item['text_description'])+'</option>'))
            });
            form.render('select'); //刷新select选择框渲染
        });
        college.setOnFail(fail);
        college.setOnError(error);
        college.setOnAfter(after);

        var building = new WkkyData();
        building.setOnBefor(befor);
        building.setOnSuccess(function (response) {
            var data = response.getData();
            $('.building').html(null).append('<option value="">请选择楼栋</option>');
            $.each(data,function (index, item) {
                $('.building').append($('<option value="'+item['building_id']+'">'+(item['text_description']==''?'(暂无名称)':item['text_description'])+'</option>'))
            });
            form.render('select'); //刷新select选择框渲染
        });
        building.setOnFail(fail);

        building.setOnError(error);
        building.setOnAfter(after);

        var room = new WkkyData();
        room.setOnBefor(befor);
        room.setOnSuccess(function (response) {
            var data = response.getData();
            $('.room').html(null).append('<option value="">请选择房间</option>');
            $.each(data,function (index, item) {
                $('.room').append($('<option value="'+item['room_id']+'">'+(item['text_description']==''?'(暂无名称)':item['text_description'])+'</option>'))
            });
            form.render('select'); //刷新select选择框渲染
        });
        room.setOnFail(fail);
        room.setOnError(error);
        room.setOnAfter(after);



        form.on('select(schoolpart)', function(data){
            clearCollege();
            window.schoolepart_id = where['schoolpart_id'] = data.value;
            window.school_text = data.othis[0].textContent;
            var formData = new FormData();
            formData.append('schoolpart_id',window.schoolepart_id);
            college.setRequest(new Request('/index/getCollege',{
                credentials: 'include',
                method: "POST",
                body:formData
            }));
            college.getDataFormRemote();
        });
        form.on('select(college)', function(data){
            clearBuilding();
            window.college_id = where['college_id'] = data.value;
            window.college_text = data.othis[0].textContent;

            var formData = new FormData();
            formData.append('college_id',window.college_id);
            building.setRequest(new Request('/index/getBuilding',{
                credentials: 'include',
                method: "POST",
                body:formData
            }));
            building.getDataFormRemote();
        });
        form.on('select(building)', function(data){
            clearRoom();
            window.building_id = where['building_id'] = data.value;
            window.building_text = data.othis[0].textContent;

            var formData = new FormData();
            formData.append('building_id',window.building_id);
            room.setRequest(new Request('/index/getRoom',{
                credentials: 'include',
                method: "POST",
                body:formData
            }));
            room.getDataFormRemote();
        });
        form.on('select(room)', function(data){
            window.room_id = where['room_id'] = data.value;
            window.room_text = data.othis[0].textContent;
            flushEquipMent();
        });

        function clearCollege() {
            window.college_id = where['college_id'] = '';
            $('.college').html(null).append('<option value="">请选择学院</option>');
            clearBuilding();
        }

        function clearBuilding() {
            window.building_id = where['building_id'] = '';
            $('.building').html(null).append('<option value="">请选择楼栋</option>');
            clearRoom();
        }

        function clearRoom() {
            window.room_id = where['room_id'] = '';
            $('.room').html(null).append('<option value="">请选择房间</option>');
            flushEquipMent();
        }

        function flushEquipMent() {
            if(where['room_id']==''){
                table.render({
                    elem: '#data-table'
                    ,cols:  [[
                        {field: 'index', title: '编号', align: 'center',width: 80}
                        ,{field: 'text_description', title: '用电设备', width: 150}
                        ,{field: 'number', title: '数量', width: 80}
                        ,{field: 'power', title: '功率 (W)', width: 100}
                    ]],
                    page: true,
                    even:true,
                    data:[]
                });
            }else{
                loading();
                table.render({
                    elem: '#data-table'
                    ,cols:  [[
                        {field: 'index', title: '编号', align: 'center',width: 80}
                        ,{field: 'text_description', title: '用电设备', width: 150}
                        ,{field: 'number', title: '数量', width: 80}
                        ,{field: 'power', title: '功率 (W)', width: 100}
                        ,{title:'操作',width: 80,align: 'center',templet:'#my-1'}
                    ]], //设置表头
                    done:function (res, curr, count) {
                        closeLoad(1);
                    },
                    method:'post',
                    limit:typeof $.cookie('limit') == typeof  undefined ? 30 :$.cookie('limit'),
                    page: true,
                    even:true,
                    where:where,
                    url:'/index/queryEquipMentByRoom',
                    loading:true
                });

                table.on('tool',function (obj) {
                    var index = layer.open({
                        type: 1
                        ,title: '报废设备' //不显示标题栏
                        ,closeBtn: false
                        ,area: ['600px;','440px;']
                        ,shade: 0.5
                        ,resize:false
                        ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                        ,btn: ['确定', '取消']
                        ,moveType: 1 //拖拽模式，0或者1
                        ,content: '<div style="margin-left: -50px;padding-bottom: 20px;padding-top: 10px;padding-right: 10px; line-height: 22px; background-color: #393D49; color: #fff; font-weight: 300;"><form class="layui-form"> <div class="layui-form-item"> <label  class="layui-form-label">数量</label> <div class="layui-input-inline"> <input type="number" title="number" name="number" class="layui-input"> </div>  </div> <div class="layui-form-item layui-form-text"> <label  class="layui-form-label">备注</label><div class="layui-input-block"><textarea style="resize: none;height: 240px;" name="desc" placeholder="请输入内容" class="layui-textarea"></textarea></div></div></form></div>',
                        btn1:function () {
                            console.log({
                                'schoolpart_id':window.schoolepart_id,
                                'schoolpart_text':window.schoolepart_text,
                                'building_id':window.building_id,
                                'building_text':window.building_text,
                                'college_id':window.college_id,
                                'college_text':window.college_text,
                                'room_id':window.room_id,
                                'room_text':window.room_text
                            });
                            layer.close(index);
                        },
                        btn2:function () {
                        }
                    });
                });
            }
        }


        function befor() {
            loading();
        }
        function fail(response) {
            layer.msg(response.getMessage(),function () {

            });
        }
        function error(response) {
            layer.msg('系统错误',function () {

            });
        }
        function after() {
            closeLoad(1);
        }
    });
});
