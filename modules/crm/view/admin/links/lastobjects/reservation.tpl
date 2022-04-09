<div class="link-last-objects m-t-30">
    <h3>{t}Недавние предварительные заказы{/t}</h3>
    <p>{t}Нажмите на один из представленных ниже предзаказов, чтобы установить с ним связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this"><span class="orderStatusColor" style="background-color:{$item->getStatusColor()}" title="{$item.__status->textView()}"></span>
                    <span class="link-last-title">{t num={$item.number|default:$item.id} date={$item.dateof|dateformat:"@date"}}Предзаказ №%num от %date{/t}</span></a>
                <div>
                    {if $item->getUser()->is_company}
                        {$buyer = $item->getUser()->getFio()}
                    {else}
                        {$buyer = $item->getUser()->company}
                    {/if}
                    <small class="c-gray">{t user=$buyer}Покупатель: %user{/t}</small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>