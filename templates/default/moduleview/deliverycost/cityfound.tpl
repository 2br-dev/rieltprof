{addjs file="%deliverycost%/deliverycost.js"}
{addcss file="%deliverycost%/deliverycost.css"} 
{assign var=deliverycost_config value=ConfigLoader::byModule('deliverycost')}
<div id="deliveryCostWrapper" class="deliveryCostWrapper formStyle {$theme}" data-dialog-options='{ "width": "320" }'>
    {if !$url->isAjax()} 
        <h1>{t}Выбор города{/t}</h1>
    {/if}           
    <div class="yourCity">
        {t}Ваш город -{/t} <b>{$city.title}</b>?
    </div>
    <div class="bottomButtons buttons cboth">
        <a href="{$redirect}" class="colorButton button color first">{t}Да{/t}</a>
        <a href="{$router->getUrl('deliverycost-front-choosecityautocomplete', ['redirect' => urlencode($redirect)])}" class="inDialog button color colorButton second">{t}Нет{/t}</a>
    </div>
</div>