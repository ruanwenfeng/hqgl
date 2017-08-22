<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:34:"../application/tpl/index/index.php";i:1503330801;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta  CONTENT="no-cache">
    <title>Title</title>
    <script type="text/javascript" data-main="__PUBLIC__/js/main"  src="__PUBLIC__/js/require.js"></script>
</head>
<body>
zheshi test1
<script type="text/javascript">
    (function () {
        var app = function () {
            require(['test']);
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
</body>
</html>