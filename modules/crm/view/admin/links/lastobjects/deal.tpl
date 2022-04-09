<div class="link-last-objects m-t-30">
    <h3>{t}Недавние сделки{/t}</h3>
    <p>{t}Нажмите на одну из представленных ниже сделок, чтобы установить с ней связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            {$status = $item->getStatus()}
            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this"><span class="orderStatusColor" style="background-color:{$status->color}" title="{$status->title}"></span>
                    <span class="link-last-title">{t num={$item.deal_num} date={$item.date_of_create|dateformat:"@date"}}Сделка №%num от %date{/t}</span></a>
                <div>
                    <small class="c-gray">{$item.title}</small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>