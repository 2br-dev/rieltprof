<h3>{t}Настройка аккаунта Assist{/t}</h3>

{t}URL для отправки результатов{/t}:<br>
<a target="_blank" href="{$router->getUrl('shop-front-onlinepay', [Act=>result, PaymentType=>$payment_type->getShortName()], true)}">
    <b>{$router->getUrl('shop-front-onlinepay', [Act=>result, PaymentType=>$payment_type->getShortName()], true)}</b>
</a>

<br><br>

URL_RETURN:<br>
<a target="_blank" href="{$SITE->getRootUrl(true)}">
    <b>{$SITE->getRootUrl(true)}</b>
</a>

<br><br>

URL_RETURN_OK:<br>
<a target="_blank" href="{$router->getUrl('shop-front-onlinepay', [Act=>success, PaymentType=>$payment_type->getShortName()], true)}">
    <b>{$router->getUrl('shop-front-onlinepay', [Act=>success, PaymentType=>$payment_type->getShortName()], true)}</b>
</a>

<br><br>
<table>
    <tr>
        <td>{t}Тип протокола{/t}:&nbsp;&nbsp;</td>
        <td><b>POST</b></td>
    </tr>
    <tr>
        <td>{t}Тип подписи{/t}:</td>
        <td><b>MD5</b></td>
    </tr>
</table>

