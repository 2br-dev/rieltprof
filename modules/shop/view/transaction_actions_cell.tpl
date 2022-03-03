{$transaction = $cell->getRow()}
{$payment_type = $transaction->getPayment()->getTypeObject()}
{if !$transaction->checkSign()}
    <b style="color:red">{t}Неверная подпись{/t}</b>
{else}
    {if $transaction->personal_account && !$payment_type->canOnlinePay() && $transaction.status == 'new' && $transaction.order_id == 0}
        <a data-confirm-text="{t}Вы действительно желаете начислить средства по данной операции?{/t}" class="crud-get uline" data-url="{$router->getAdminUrl('setTransactionSuccess', ['id' => $transaction.id])}">{t}начислить средства{/t}</a>
    {/if}

    {if !$payment_type->canOnlinePay() && $transaction.status == 'new' && $transaction.order_id > 0}
        <a data-confirm-text="{t}Вы действительно желаете оплатить заказ и выбить чек?{/t}" class="crud-get uline" data-url="{$router->getAdminUrl('setTransactionSuccess', ['id' => $transaction.id])}">{t}оплатить заказ{/t}</a>
    {/if}

    {if $transaction.status == 'success' && $transaction.cost > 0 && ($transaction.receipt == 'no_receipt' || $transaction.receipt == 'fail')}
        <a data-confirm-text="{t}Вы действительно желаете выбить чек по данной операции?{/t}" class="crud-get uline" data-url="{$router->getAdminUrl('sendReceipt', ['id' => $transaction.id])}">{t}выбить чек{/t}</a>
    {/if}
    
    {if $transaction.status == 'success' && $transaction.receipt == 'receipt_success' && $transaction->isPossibleRefundReceipt()}
        <a data-confirm-text="{t}Вы действительно желаете выбить чек возврата по данной операции?{/t}" class="crud-get uline" data-url="{$router->getAdminUrl('sendRefundReceipt', ['id' => $transaction.id])}">{t}сделать чек возврата{/t}</a>
    {/if}
{/if}

{foreach $transaction->getAvailableActionsList() as $action}
    <div>
        <a href='{$action->getHref()}' data-confirm-text='{$action->getConfirmText()}' class='crud-get uline'>
            {$action->getTitle()}
        </a>
    </div>
{/foreach}