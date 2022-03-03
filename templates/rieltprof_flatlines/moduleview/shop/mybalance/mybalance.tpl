{* Шаблон страницы просмотра баланса пользователя в личном кабинете *}

<div class="form-style">
    <ul class="nav nav-tabs hidden-xs hidden-sm">
        <li class="active">
            <a>{t}История операций{/t}</a>
        </li>
        <li>
            <a href="{$router->getUrl('shop-front-mybalance', [Act=>addfunds])}">{t}Пополнить баланс{/t}</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="visible-xs visible-sm hidden-md hidden-lg mobile_nav-tabs">
            <span>{t}История операций{/t}</span>
        </div>
        <div>
            <h2 class="h2 t-balance_title">{t}Ваш баланс{/t}: {$balance_string}</h2>
            <p class="visible-xs visible-sm text-center"><a href="{$router->getUrl('shop-front-mybalance', [Act=>addfunds])}">{t}Пополнить баланс{/t}</a></p>
            {if $list}
                <ul class="t-balance-list">
                    {foreach $list as $item}
                        <li>
                            <div class="t-balance_left">№ {$item.id}<br> от {$item.dateof|date_format:"d.m.Y H:i"}</div>
                            <div class="t-balance_center">{$item->reason}</div>
                            {if !$item->order_id && $item->cost > 0}
                                <div class="t-balance_right green">+{$item->getCost(false, true)}</div>
                            {/if}
                            {if $item->order_id}
                                <div class="t-balance_right red">-{$item->getCost(false, true)}</div>
                            {else}
                                {if $item->cost < 0}
                                    <div class="t-balance_right red">{$item->getCost(false, true)}</div>
                                {/if}
                            {/if}
                        </li>
                    {/foreach}
                </ul>
            {/if}
        </div>
    </div>

    {include file="%THEME%/paginator.tpl"}
</div>