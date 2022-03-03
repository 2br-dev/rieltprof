{extends file="%install%/wrap.tpl"}
{block name="content"}

{addjs file="malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js" basepath="common"}
{addjs file="%install%/install.js"}

<noscript>
<div class="no-javascript">
    {t}Для продолжения установки необходимо включить поддержку JavaScript у Вашего браузера{/t}
</div>
</noscript>

<div class="lang-select">
    <span class="lang-word">{t}Язык{/t}</span>&nbsp;&nbsp;
    <div class="rs-group lang-select-list">
        <span class="rs-active">{$locale_list[$current_lang]}</span>
        <ul class="rs-dropdown">
            {foreach $locale_list as $locale_key => $locale}
                {if $current_lang != $locale_key}
                    <li {if $locale@first}class="first"{/if}><a href="{$router->getUrl('install', ['Act' => 'changeLang', 'lang' => $locale_key])}">{$locale}</a></li>
                {/if}                    
            {/foreach}
        </ul>
    </div>
</div>

<h2>{t}Лицензионное соглашение{/t}</h2>

<div class="scroll-block license-text">
    {$license_text}
</div>
<div class="button-line mtop30">
    <a data-href="{$router->getUrl('install', ['step' => '2'])}" class="next disabled">{t}далее{/t}</a>
    <div class="iagree">
        <input type="checkbox" id="iagree">&nbsp; <label for="iagree"><strong>{t}Я соглашаюсь с условиями лицензионного соглашения{/t}</strong></label>
    </div>
</div>

{/block}