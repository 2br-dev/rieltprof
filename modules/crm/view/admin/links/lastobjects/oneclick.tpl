<div class="link-last-objects m-t-30">
    <h3>{t}Недавние покупки в 1 клик{/t}</h3>
    <p>{t}Нажмите на одну из представленных ниже покупок в 1 клик, чтобы установить с ней связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this"><span class="orderStatusColor" style="background-color:{$item->getStatusColor()}" title="{$item.__status->textView()}"></span>
                    <span class="link-last-title">{t num={$item.number|default:$item.id} date={$item.dateof|dateformat:"@date"}}Покупка №%num от %date{/t}</span></a>
                <div>
                    <small class="c-gray">{t user=$item.user_fio}Покупатель: %user{/t}</small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>