<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta  CONTENT="no-cache">
    <title>Title</title>
    <script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>
</head>
<body>
{extend name="base" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<div class="layui-container">
    <div class="layui-row">
        <div class="layui-col-md9">
            你的内容 9/12
        </div>
        <div class="layui-col-md3">
            你的内容 3/12
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['index']);
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