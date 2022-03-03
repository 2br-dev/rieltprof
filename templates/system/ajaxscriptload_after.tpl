{* Отложенная загрузка сриптов и стилей *}
{foreach from=$app->getCss('header')+$app->getCss('footer') item=css}
{$css.params.before}<link type="{$css.params.type|default:"text/css"}" href="{$css.file}" media="{$css.params.media|default:"all"}" rel="{$css.params.rel|default:"stylesheet"}">{$css.params.after}
{/foreach}
{$jslist = $app->getJs('header') + $app->getJs('footer')}
{if count($jslist)}
    <script>$LAB.loading = true; var _lab = $LAB;</script>
    {foreach from=$jslist item=js}
    {$js.params.before}<script>_lab = _lab.{if $js.params.waitbefore}wait().{/if}script('{$js.file}');</script>{$js.params.after}
    {/foreach}
    <script>
        _lab.wait(function() {
            $LAB.loading = false;
            $(window).trigger('LAB-loading-complete');
        });
    </script>
{else}
    <script>
        $LAB.loading = false;
        $(window).trigger('LAB-loading-complete');
    </script>
{/if}