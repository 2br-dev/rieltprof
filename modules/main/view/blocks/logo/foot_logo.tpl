{if $site_config.logo}
    {if $link != ' '}<a href="{$link}">{/if}
    <img src="{$site_config.__logo->getUrl($width, $height)}" alt=""/>
    {if $link != ' '}</a>{/if}
{else}
    {include file="theme:default/block_stub.tpl"  class="noBack blockSmall blockLeft blockLogo" do=[
        {adminUrl do=false mod_controller="site-options"}    => t("Добавьте логотип")
    ]}
{/if}
<div class="copy">&copy; {"now"|date_format:"%Y"} {t}Все права защищены{/t}</div>
<br>
<a href="http://readyscript.ru" class="cms">{t}Работает на <span class="cmsName">ReadyScript</span>{/t}</a>