<div class="widget" wid="{$widget.id}" wclass="{$widget.class}" data-positions='{$widget.item->getPositionsJson()}'>
{$app->autoloadScripsAjaxBefore()}
    <div class="widget-border">
        <div class="widget-head">
            <div class="widget-title">{$widget.title}</div>
            <div class="widget-tools">
                {foreach $widget.self->getTools() as $tool}
                    <a {foreach $tool as $key => $value}{if $key[0] == '~'} {$key|replace:"~":""}='{$value}'{else} {$key}="{$value}"{/if}{/foreach}></a>
                {/foreach}
                <a class="widget-close zmdi zmdi-close" title="{t}Скрыть виджет{/t}"></a>
            </div>
        </div>  
        <div class="widget-content updatable" data-url="{adminUrl mod_controller=$widget.class do=false}" data-update-block-id="{$widget.class}">
            {$widget.inside_html}
        </div>
    </div>
{$app->autoloadScripsAjaxAfter()}    
</div>