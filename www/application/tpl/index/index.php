{extend name="index" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<iframe style="border:none;margin: 0;padding: 0;box-sizing: border-box;width: 100%;height: 100%"></iframe>
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