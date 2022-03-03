{$receipt_info = $data->receipt->getReceiptInfo()}
{t}Чек:{/t}
{if $value=$receipt_info->getReceiptDatetime()}{t}Дата и время{/t}:{$value|dateformat:"@date @time:@sec"};{/if}
{if $value=$receipt_info->getTotal()}{t}Сумма{/t}:{$value|format_price};{/if}
{if $value=$receipt_info->getFnNumber()}{t}Номер фискального накопителя{/t}:{$value};{/if}
{if $value=$receipt_info->getEcrRegistrationNumber()}{t}Регистрационный номер ККТ{/t}:{$value};{/if}
{if $value=$receipt_info->getFiscalDocumentNumber()}{t}Фискальный номер документа{/t}:{$value};{/if}
{if $value=$receipt_info->getFiscalDocumentAttribute()}{t}Фискальный признак документа{/t}:{$value};{/if}
{if $value=$receipt_info->getReceiptOfdUrl()}{t}Ссылка на проверку чека:{/t}{$value}{/if}