{extends file="%alerts%/notice_template.tpl"}
{block name="content"}
    {$receipt_info = $data->receipt->getReceiptInfo()}
    <h1>{t}Спасибо за покупку!{/t}</h1>

    <p>{t}Информация о чеке{/t}</p>
    <table class="otable" border="0" cellpadding="5">
        {if $value=$receipt_info->getReceiptDatetime()}
            <tr>
                <td><b>{t}Дата и время регистрации чека{/t}</b></td>
                <td>{$value|dateformat:"@date @time:@sec"}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getTotal()}
            <tr>
                <td><b>{t}Сумма{/t}</b></td>
                <td>{$value|format_price}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getFnNumber()}
            <tr>
                <td><b>{t}Номер фискального накопителя{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getEcrRegistrationNumber()}
            <tr>
                <td><b>{t}Регистрационный номер ККТ{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getFiscalDocumentNumber()}
            <tr>
                <td><b>{t}Фискальный номер документа{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getFiscalDocumentAttribute()}
            <tr>
                <td><b>{t}Фискальный признак документа{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}
    </table>

    {if $value=$receipt_info->getReceiptOfdUrl()}
        <p>{t}Электронный чек доступен для проверки на адресу <a href="{$value}" target="_blank">{$value}</a>{/t}</p>
    {/if}
{/block}