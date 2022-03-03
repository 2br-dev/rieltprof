{$show=false}
{foreach $template_hooks as $hook_id => $hook_data}
{if isset($hook_handlers[$hook_id]) && count($hook_handlers[$hook_id])>1}{$show=true}{/if}
{/foreach}

<div data-dialog-options='{ "width":700,"height":600 }'></div>
{if $show}
    {addjs file="%templates%/hooksort.js"}
    <div class="formbox hook-sort-container">
        <div class="inform-block">
            {t}Укажите порядок, в котором модули будут добавлять свой контент к каждой зоне данного шаблона{/t}
        </div>
        <br>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            {foreach $template_hooks as $hook_id => $hook_data}
                {if count($hook_handlers[$hook_id])>1}
                <div class="hook-sort-block">
                    <div class="hook-sort-title">{$hook_data.title|default:t('Нет названия')} <span class="hook-sort-id">({$hook_id})</span></div>
                    <ul class="hook-sort-modules">
                        {foreach $hook_handlers[$hook_id] as $module_id => $module}
                            <li><span class="hook-sort-handler"><i class="zmdi zmdi-unfold-more"></i></span> {$module.module_title}
                                <input type="hidden" name="sort_data[{$hook_id}][]" value="{$module_id}">
                            </li>
                        {/foreach}
                    </ul>
                </div>
                {/if}            
            {/foreach}
        </form>
    </div>
{else}
    <div class="inform-block">
        {t}Сортировка модулей не требуется, менее двух модулей дополняют зоны шаблона{/t}
    </div>
{/if}