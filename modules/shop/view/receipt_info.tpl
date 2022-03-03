{$extra_info = $receipt->getExtraInfo('success_info')}
{if !empty($extra_info)}
    {$receipt_info = $receipt->getReceiptInfo()}
    <div class="titlebox">{t}Информация о чеке{/t}</div>
    <table class="otable" border="0" cellpadding="5">
        {if $value=$receipt_info->getFiscalReceiptNumber()}
            <tr>
                <td><b>{t}Номер чека в смене{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}

        {if $value=$receipt_info->getShiftNumber()}
            <tr>
                <td><b>{t}Номер смены{/t}</b></td>
                <td>{$value}</td>
            </tr>
        {/if}

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

        {if $value=$receipt_info->getReceiptOfdUrl()}
            <tr>
                <td><b>{t}Ссылка на чек в ОФД{/t}</b></td>
                <td><a class="btn btn-default" href="{$value}" target="_blank">{t}Посмотреть{/t}</a></td>
            </tr>
        {/if}

        {if $value=$receipt_info->getQrCodeImageUrl()}
            <tr>
                <td><b>{t}QR код для проверки{/t}</b></td>
                <td><img src="{$value}" width="200" height="200"></td>
            </tr>
        {/if}

    </table>
{else}
    {t}Нет информации{/t}
{/if}