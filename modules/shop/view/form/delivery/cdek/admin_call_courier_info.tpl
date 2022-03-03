<div class="delivery-order-view updatable" data-url="{$url->getSelfUrl()}">
    <table class="otable">
        <tr>
            <td class="otitle">{t}Дата ожидания курьера{/t}</td>
            <td>{$info.intake_date}</td>
        </tr>
        <tr>
            <td class="otitle">{t}Время ожидания курьера{/t}</td>
            <td>{$info.intake_time_from} - {$info.intake_time_to}</td>
        </tr>
        {if $info.lunch_time_from || $info.lunch_time_to}
            <tr>
                <td class="otitle">{t}Время Перерыва на обед{/t}</td>
                <td>{$info.lunch_time_from} - {$info.lunch_time_to}</td>
            </tr>
        {/if}
        {if $info.comment}
            <tr>
                <td class="otitle">{t}Комментарий{/t}</td>
                <td>{$info.comment}</td>
            </tr>
        {/if}
        <tr>
            <td class="otitle">qwe</td>
            <td>{var_dump($info)}</td>
        </tr>
    </table>
</div>