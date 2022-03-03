{$transactions = $elem->getTransactions()}

{if $transactions}
    <div class="order-transactions">
        <h4>{t}Список транзакций{/t}</h4>
        <div class="order-transactions_list">
            {foreach $transactions as $transaction}
                {if $transaction.status == 'success'}
                    {$title_class = 'text-success'}
                {elseif $transaction.status == 'fail'}
                    {$title_class = 'text-danger'}
                {elseif $transaction.status == 'hold'}
                    {$title_class = 'text-info'}
                {else}
                    {$title_class = ''}
                {/if}
                <div class="order-transactions_item">
                    <div class="order-transactions_item-title">
                        <strong class="{$title_class}">№ {$transaction['id']} &mdash; {$transaction['reason']} &mdash; {$transaction['__status']->textView()}</strong>
                        <span>
                            <a href="{$router->getAdminUrl(false, ['f' => ['id' => $transaction['id']]], 'shop-transactionctrl', true)}">
                                {t}Перейти{/t}
                            </a>
                        </span>
                    </div>
                    {$actions_list = $transaction->getAvailableActionsList($elem)}
                    {if $actions_list}
                        <div class="order-transactions_item-actions">
                            {foreach $actions_list as $action}
                                <a href='{$action->getHref()}' data-confirm-text='{$action->getConfirmText()}' class='crud-get transaction-action btn btn-sm btn-alt {$action->getCssClass()}'>
                                    {$action->getTitle()}
                                </a>
                            {/foreach}
                        </div>
                    {/if}
                    <div class="order-transactions_item-logs">
                        {foreach $transaction->getChangeLogs() as $log}
                            <div>
                                <span>{$log.date|dateformat: "@date @time"} &mdash; {$log.change}</span>
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
{/if}
