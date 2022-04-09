<div class="link-last-objects m-t-30">
    <h3>{t}Недавние звонки{/t}</h3>
    <p>{t}Нажмите на один из представленных ниже звонков, чтобы установить с ним связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            {if $item->call_flow == 'in'}
                {$number = $item->caller_number}
            {else}
                {$number = $item->called_number}
            {/if}

            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this">
                    <span class="link-last-title">{t num=$number date={$item.event_time|dateformat:"@date @time"}}%num от %date{/t}</span></a>
                <div>
                    <small class="c-gray">{$item.__call_flow->textView()}. {$item->getOtherUser()->getFio()}</small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>