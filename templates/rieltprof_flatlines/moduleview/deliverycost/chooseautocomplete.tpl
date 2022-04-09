{addjs file="%deliverycost%/deliverycost.js"}
{addcss file="%deliverycost%/deliverycost.css"} 
{assign var=deliverycost_config value=ConfigLoader::byModule('deliverycost')}
<div id="deliveryCostWrapper" class="deliveryCostWrapper" data-dialog-options='{ "width": "500" }'>
    {if $success}
        <script>
            $(function(){
                //Делаем редирект
                if ($.colorbox){ //Если мы в открытом окне
                    $.colorbox.close();    
                }
                document.location = '{$redirect}';  
            });
        </script>
    {else}
        <form action="{$router->getUrl('deliverycost-front-choosecityautocomplete')}" method="POST" class="formStyle">
            <input type="hidden" name="redirect" value="{$redirect|default:'/'}"/>
            {if $url->isAjax()} 
                <h2 class="dialogTitle">{t}Выбор города{/t}</h2>  
            {else} 
                <h1>{t}Выбор города{/t}</h1> 
            {/if}           
            <div class="row">
                <div class="queryCityWrap" id="queryCityBox">
                    <input type="text" class="query cityautocomplete" name="city_name" value="" placeholder="{t}Наберите город и выберите его{/t}" autocomplete="off"/>
                </div>
            </div>
            <div class="inputBlock"></div>
            <div class="row buttonsLine deliveryCostShowButton" style="display: none;">
                <input type="submit" class="formSave" value="{t}Сохранить{/t}"/>
            </div>
        </form>
        
        <script>
            $(function() {
                $('.deliveryCostWrapper').deliveryCost();
            });
        </script>
    {/if}
</div>