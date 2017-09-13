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
        var where = {};
        var college = new WkkyData();
        college.setOnSuccess(function (response) {
            var data = response.getData();
            $('.college').html(null).append('<option value="">请选择学院</option>');
            $.each(data,function (index, item) {
                $('.college').append($('<option value="'+item['college_id']+'">'+(item['text_description']==''?'(暂无名称)':item['text_description'])+'</option>'))
            });
            form.render('select'); //刷新select选择框渲染
        });
        form.on('radio',function (data) {
            window.mode = data.value;
            if(data.value == 1){
                window.college_id = where['college_id'] = '';
                $('.div-college').css('display','none');
                countSchoolPart(where);
            }else{
                $('.div-college').css('display','inline-block');
                var formData = new FormData();
                formData.append('schoolpart_id',window.schoolepart_id);
                college.setRequest(new Request('/index/getCollege',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                }));
                college.getDataFormRemote();
                countCollege(where);
            }
        });

        form.on('select(schoolpart)', function(data){
            window.schoolepart_id = where['schoolpart_id'] = data.value;
            window.college_id = where['college_id'] = '';
            if(window.mode == 1)
                countSchoolPart(where);
            else{
                var formData = new FormData();
                formData.append('schoolpart_id',window.schoolepart_id);
                college.setRequest(new Request('/index/getCollege',{
                    credentials: 'include',
                    method: "POST",
                    body:formData
                }));
                college.getDataFormRemote();
                countCollege(where);
            }
        });

        form.on('select(college)', function(data){
            console.log(data);
            window.college_id = where['college_id'] = data.value;
            if(window.mode == 1){
            }
            else{
                countCollege(where);
            }
        });

        form.on('select(equipmentType)', function(data){
            where['text_description'] = data.value;
            if(window.mode == 1)
                countSchoolPart(where);
            else
                countCollege(where);
        });
        
        function countSchoolPart(where) {
            loading();
            table.render({
                elem: '#data-table'
                ,cols:  [[ //标题栏
                    {field: 'index', title: '编号', align: 'center',width: 80},
                    {field: 'text_description', title: '校区', sort: true, width: 320}
                    ,{field: 'equiptype', title: '设备类型', sort: true, width: 250}
                    ,{field: 'number',title:'数量',width: 80,sort: true}
                ]], //设置表头
                method:'post',
                done:function () {
                  closeLoad(1);
                },
                limit:typeof $.cookie('limit') == typeof  undefined ? 30 :$.cookie('limit'),
                page: true,
                even:true,
                where:where,
                url:'/index/countSchoolPartEquipMent',
                loading: true
            });
        }

        function countCollege() {
            loading();
            table.render({
                elem: '#data-table'
                ,cols:  [[ //标题栏
                    {field: 'index', title: '编号', align: 'center',width: 80},
                    {field: 'schoolpart', title: '校区', sort: true, width: 320},
                    {field: 'text_description', title: '学院', sort: true, width: 320}
                    ,{field: 'equiptype', title: '设备类型', sort: true, width: 250}
                    ,{field: 'number',title:'数量',width: 80,sort: true}
                ]], //设置表头
                method:'post',
                done:function () {
                    closeLoad(1);
                },
                limit:typeof $.cookie('limit') == typeof  undefined ? 30 :$.cookie('limit'),
                page: true,
                even:true,
                where:where,
                url:'/index/countCollegeEquipMent',
                loading: true
            });
        }
    });
});
