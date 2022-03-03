{foreach from=$app->getCss('header', true) item=css}
{$css.params.before}<link {if $css.params.type !== false}type="{$css.params.type|default:'text/css'}"{/if} href="{$css.file}" {if $css.params.media!==false}media="{$css.params.media|default:'all'}"{/if} rel="{$css.params.rel|default:'stylesheet'}">{$css.params.after}
{/foreach}
{foreach from=$app->getJs('header', true) item=js}
{$js.params.before}<script type="{$js.params.type|default:'text/javascript'}" src="{$js.file}"></script>{$js.params.after}
{/foreach}
{$wrapped_content}
{* Нижние скрипты *}
{foreach from=$app->getCss('footer', true) item=css}
    {$css.params.before}<link {if $css.params.type !== false}type="{$css.params.type|default:'text/css'}"{/if} href="{$css.file}" {if $css.params.media!==false}media="{$css.params.media|default:'all'}"{/if} rel="{$css.params.rel|default:'stylesheet'}">{$css.params.after}
{/foreach}
{foreach from=$app->getJs('footer', true) item=js}
{$js.params.before}<script type="{$js.params.type|default:'text/javascript'}" src="{$js.file}"></script>{$js.params.after}
{/foreach}    