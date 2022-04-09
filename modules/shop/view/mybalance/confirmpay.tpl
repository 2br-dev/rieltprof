{* Шаблон страницы подтверждения оплаты с лицевого счета *}

<div class="section d-flex justify-content-center">
    <div class="col-md-9 col-lg-6">
        <h1>{t}Подтверждение оплаты{/t}</h1>

        {if $api->hasError()}
            <div class="invalid-feedback d-block">
                {$api->getErrorsStr()}
            </div>
        {/if}

        <ul class="item-product-chars mt-3">
            <li>
                <span class="text-gray pe-1 bg-body">Сумма</span>
                <span class="ms-2 bg-body fw-bold"> {$transaction->getCost(true, true)}</span>
            </li>
            <li>
                <span class="text-gray pe-1 bg-body">{t}Назначение платежа{/t}</span>
                <span class="ms-2 bg-body"> {$transaction->reason}</span>
            </li>
            <li>
                <span class="text-gray pe-1 bg-body">{t}Источник{/t}</span>
                <span class="ms-2 bg-body"> {t}Лицевой счет{/t}</span>
            </li>
        </ul>

        {if !$api->hasError()}
            <form method="POST" class="mt-5">
                <button type="submit" class="btn btn-primary">{t}Оплатить{/t}</button>
            </form>
        {/if}
    </div>
</div>