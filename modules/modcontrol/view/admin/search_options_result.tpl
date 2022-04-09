{if $term}
    {if $tree}
        <ul class="modules-names">
        {foreach $tree as $module}
            <li>
                <a href="{$module.url}"><i class="zmdi zmdi-puzzle-piece"></i> {$module.title}</a>
                <ul class="tabs-names">
                    {foreach $module.items as $tab}
                        <li>
                            <a href="{$tab.url}"><i class="zmdi zmdi-tab"></i>{$tab.title}</a>
                            <ul class="field-names">
                                {foreach $tab.items as $field}
                                    <li>
                                        <a href="{$field.url}"><i class="zmdi {if $field.is_tool}zmdi-settings{else}zmdi-toll{/if}"></i> {$field.field_title}</a>
                                        {if $field.field_hint}
                                            <div class="field-help">{$field.field_hint}</div>
                                        {/if}
                                    </li>
                                {/foreach}
                            </ul>
                        </li>
                    {/foreach}
                </ul>
            </li>
        {/foreach}
        </ul>
    {else}
        <div>{t}По вашему запросу ничего не найдено{/t}</div>
    {/if}
{else}
    {t}Здесь будет результат поиска{/t}
{/if}