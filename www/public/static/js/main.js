/**
 * Created by Administrator on 2017/8/18.
 */
require.config({
    paths : {
        "jquery" : ['http://libs.baidu.com/jquery/2.0.3/jquery'],
        'layui':'../layui/layui',
        'test':'test',
        'highcharts':['https://cdn.hcharts.cn/highcharts/highcharts']
    }
});
window.complete = true;

window.HandleResponse = function (json) {
    if(json===null || typeof json === typeof undefined){
        console.error('参数不能为空');
        return;
    }
    this.json = json;
};

window.HandleResponse.prototype = {
    constructor:window.HandleResponse,
    isSuccess:function () {
        return this.json['status'] === 1;
    },
    getData:function (index) {
        if(typeof index === typeof undefined)
            index = 0;
        if(index===true)
            return this.json['data'];
        else  if(this.json['data'].length > index)
            return this.json['data'][index];
        else
            return undefined;
    },
    getExtra:function (key) {
        if(this.json['extra'].hasOwnProperty (key))
            return this.json['extra'][key];
        return this.json['extra'];
    },
    getMeta:function (index) {
        if(typeof index === typeof undefined)
            index = 0;
        if(index===true)
            return this.json['meta'];
        else if(this.json['meta'].length > index)
            return this.json['meta'][index];
        else
            return undefined;
    },
    getMessage:function () {
        return this.json['message'];
    }
};

window.WkkyData = function (url,init,data) {
    this.init = init || {};
    this.url = url;
    this.requestData = data || {};
    this.responseData = null;
    this.request = null;
    this._befor = [];    //请求前调用
    this._after = [];    //请求结束调用
    this._error = [];    //请求出错调用
    this._success = [];  //操作成功调用
    this._fali = [];     //操作失败调用
};

window.WkkyData.prototype = {
    constructor : window.View,
    getDataFormRemote:function () {
        if(this.request===null){
            this.createFormData();
            this.createRequest();
        }
        var _this = this;
        _this.befor(this.request);
        window.fetch(this.request).then(function (response) {
            if(response.ok){
                return response.json();
            }else{
                throw response;
            }
        }).then(function (json) {
            _this.responseData = json;
            var handleResponse = new window.HandleResponse(json);
            if(handleResponse.isSuccess()){
                //操作成功
                _this.success(handleResponse);
            }else{
                //操作失败
                _this.fali(handleResponse);
            }
            _this.after(handleResponse);
        }).catch(function (e) {
            _this.error(e);
        });
    },

    befor:function (request) {
        $.each(this._befor,function (index,item) {
            return item(request);
        });
    },
    after:function (handleResponse) {
        $.each(this._after,function (index,item) {
            return item(handleResponse);
        });
    },
    success:function (handleResponse) {
        $.each(this._success,function (index,item) {
            return item(handleResponse);
        });
    },
    fali:function (handleResponse) {
        $.each(this._fali,function (index,item) {
            return item(handleResponse);
        });
    },
    error:function (e) {
        console.error(e);
        $.each(this._error,function (index,item) {
            return item(e);
        });
        this.after(e);
    },
    getData:function () {
        return this.responseData;
    },

    setOnBefor:function (callback) {
        this._befor.push(callback);
    },

    setOnAfter:function (callback) {
        this._after.push(callback);
    },

    setOnSuccess:function (callback) {
        this._success.push(callback);
    },

    setOnFail:function (callback) {
        this._fali.push(callback);
    },

    setOnError:function (callback) {
        this._error.push(callback);
    },

    setRequest:function (request) {
        this.request = request;
    },

    createRequest:function () {
        this.request = new Request(this.url,this.init);
    },
    createFormData:function () {
        var formData = new FormData();
        for (var i in this.requestData){
            if(this.requestData.hasOwnProperty(i)){
                formData.append(i,this.requestData[i]);
            }
        }
        this.init['body'] = formData;
    }
};

window.loading = function (icon,shade) {
    if(typeof icon == typeof undefined){
        icon = 1;
    }
    if(typeof shade == typeof undefined){
        shade = 0.2
    }
    load = layer.load(icon,{
        shade: shade //不显示遮罩
    });
};
window.closeLoad = function (max) {
    if(++flag >= max){
        flag = 0;
        layer.close(load);
    }
};