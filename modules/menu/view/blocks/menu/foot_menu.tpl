{if $items}
    <nav class="bottomMenu">
        <h4>О НАС</h4>
        {foreach from=$items item=item}
            <ul>
                <li class="{if $item->getChildsCount()}node{/if}{if $item.fields->isAct()} act{/if}" {$item.fields->getDebugAttributes()}>
                    <a href="{$item.fields->getHref()}"
                       {if $item.fields.target_blank}target="_blank"{/if}>{$item.fields.title}</a>
                </li>
            </ul>
        {/foreach}
    </nav>
{else}
    {include file="theme:default/block_stub.tpl"  class="noBack blockSmall blockLeft blockLogo" do=[
    {$this_controller->getSettingUrl()}    => t("Настройте блок")
    ]}
{/if}
