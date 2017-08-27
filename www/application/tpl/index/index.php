{extend name="index" /}
{block name="css"}
<link rel="stylesheet" href="__PUBLIC__/css/index.css">
{/block}
{block name="page-body"}
<iframe class="iframe-body">

</iframe>
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