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
    window.building_id = '';
    window.room_id = '';
    window.equiptype = '';
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

        var equipment = new WkkyData();
        equipment.setOnBefor(befor);
        equipment.setOnSuccess(function (response) {
            var data = response.getData();
            data = data[0];
            $('form input[name=text_description]').val(data['text_description']);
            $('form input[name=name]').val(data['name']);
            $('form input[name=brand]').val(data['brand']);
            $('form input[name=power]').val(data['power']);
            $('form input[name=day_time]').val(data['day_time']);
            $('form input.info-input').attr('readonly',true);
            $('form input[name=mode]').val(1);
        });
        equipment.setOnFail(fail);

        equipment.setOnError(error);
        equipment.setOnAfter(after);

        var record = new WkkyData();
        record.setOnBefor(befor);
        record.setOnSuccess(function (response) {
            $('button[type=reset]').click();
            layer.msg(response.getMessage());
        });
        record.setOnFail(fail);
        record.setOnError(error);
        record.setOnAfter(after);

        form.on('select(schoolpart)', function(data){
            clearCollege();
            window.schoolepart_id = where['schoolpart_id'] = data.value;
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
            clearEquipMent();
            window.room_id = where['room_id'] = data.value;
            $('.equipmentType').attr('disabled',false);
            form.render('select'); //刷新select选择框渲染
        });
        form.on('select(equipmentType)', function(data){
            if(data.value==''){
                $('form input.info-input').attr('readonly',false);
            }
            else {
                window.equiptype = where['equiptype'] = data.value;
                var formData = new FormData();
                formData.append('equiptype',window.equiptype);
                formData.append('room_id',window.room_id);
                equipment.setRequest(new Request('/index/getEquipMent',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                }));
                equipment.getDataFormRemote();
            }
        });
        form.on('submit(add)',function (e) {
            var data = e.field;
            if(data['room_id']==''){
                layer.msg('请选择房间',function () {

                });
                return false;
            }
            data['text_description'] =$.trim(data['text_description']);
            if(data['text_description'].length==0){
                layer.msg('设备描述不能为空',function () {

                });
                return false;
            }
            var value =  Number(data['power']);
            if(isNaN(value) || Math.floor(value) !== value||value<=0){
                layer.msg('功率只能是正整数',function () {
                    
                });
                return false;
            }

            value =  Number(data['number']);
            if(isNaN(value) || Math.floor(value) !== value||value<=0){
                layer.msg('数量只能是正整数',function () {

                });
                return false;
            }

            value =  Number(data['day_time']);
            if(isNaN(value) || Math.floor(value) !== value||value<=0){
                layer.msg('每日时长只能是正整数',function () {

                });
                return false;
            }
            var formData = new FormData();
            for (var item in data){
                if(data.hasOwnProperty(item))
                    formData.append(item,data[item]);
            }
            record.setRequest(new Request('/index/tryAddEquipMent',{
                credentials: 'include',
                method: "POST",
                body:formData
            }));
            record.getDataFormRemote();
            return false;
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
            clearEquipMent();
            window.room_id = where['room_id'] = '';
            $('.room').html(null).append('<option value="">请选择房间</option>');
        }
        function clearEquipMent() {
            $(".equipmentType option:first").prop("selected", 'selected');
            window.room_id = where['equiptype'] = '';
            $('.equipmentType').attr('disabled',true);
            $('form input.info-input').attr('readonly',false);
            $('form input[name=mode]').val(2);
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
