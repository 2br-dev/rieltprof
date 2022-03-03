{if $error}
    <div class="empty-widget">
        {$error}
    </div>
{elseif $items}
    <div class="best-sellers owl-carousel owl-theme">
        {foreach $items as $item}
            <div class="best-sellers_item">
                <h2 class="best-sellers_title">{$item.title}</h2>
                <div class="best-sellers_description">
                    <p>{$item.description}</p>
                </div>
                <div class="best-sellers_actions">
                    <a href="{$this_controller->api->prepareLink($item.link)}" target="_blank" class="btn btn-default btn-alt best-sellers_action">{$item.link_title|default:"{t}Узнать больше{/t}"}</a>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <div class="empty-widget">
        {t}Нет предложений{/t}
    </div>
{/if}