<!DOCTYPE {$app->getDoctype()}>
<html {$app->getHtmlAttrLine()} {if $SITE.language}lang="{$SITE.language}"{/if}>
<head {$app->getHeadAttributes(true)}>
<title>{$app->title->get()}</title>
{$app->meta->get()}
{foreach $app->getCss() as $css}
{$css.params.before}<link {if $css.params.type !== false}type="{$css.params.type|default:"text/css"}"{/if} href="{$css.file}" {if $css.params.media!==false}media="{$css.params.media|default:"all"}"{/if} rel="{$css.params.rel|default:"stylesheet"}"{if $css.params.as} as="{$css.params.as}"{/if}{if $css.params.crossorigin} crossorigin="{$css.params.crossorigin}"{/if}>{$css.params.after}
{/foreach}
<script>
    var global = {$app->getJsonJsVars()};
</script>
{foreach $app->getJs() as $js}
{$js.params.before}<script type="{$js.params.type|default:"text/javascript"}" src="{$js.file}"{if $js.params.async} async{/if}{if $js.params.defer} defer{/if}></script>{$js.params.after}
{/foreach}
{if !empty($app->getJsCode('header'))}
<script type="text/javascript">{$app->getJsCode('header')}</script>
{/if}
{$app->getAnyHeadData()}
</head>
<body {if $app->getBodyClass()!= ''}class="{$app->getBodyClass()}"{/if} {$app->getBodyAttrLine()}>
    {$body}
    {* Нижние стили *}
    {foreach $app->getCss('footer') as $css}
    {$css.params.before}<link {if $css.params.type !== false}type="{$css.params.type|default:"text/css"}"{/if} href="{$css.file}" {if $css.params.media!==false}media="{$css.params.media|default:"all"}"{/if} rel="{$css.params.rel|default:"stylesheet"}">{$css.params.after}
    {/foreach}
    {* Нижние скрипты *}
    {foreach $app->getJs('footer') as $js}

    {$js.params.before}<script type="{$js.params.type|default:"text/javascript"}" src="{$js.file}"{if $js.params.async} async{else} defer{/if}></script>{$js.params.after}
    {/foreach}
    {if !empty($app->getJsCode('footer'))}
        <script type="text/javascript">{$app->getJsCode('footer')}</script>
    {/if}
</body>
</html>