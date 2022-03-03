<h3>{t}Настройка аккаунта PayPal{/t}</h3>

<b>Notification URL: </b><br>
<a target="_blank" href="{$router->getUrl('shop-front-onlinepay', [Act=>result, PaymentType=>$payment_type->getShortName()], true)}">
    {$router->getUrl('shop-front-onlinepay', [Act=>result, PaymentType=>$payment_type->getShortName()], true)}
</a>

<br><br>

<b>Success URL: </b><br>
<a target="_blank" href="{$router->getUrl('shop-front-onlinepay', [Act=>success, PaymentType=>$payment_type->getShortName()], true)}">
    {$router->getUrl('shop-front-onlinepay', [Act=>success, PaymentType=>$payment_type->getShortName()], true)}
</a>

<br><br>

<b>Fail URL: </b><br>
<a target="_blank" href="{$router->getUrl('shop-front-onlinepay', [Act=>fail, PaymentType=>$payment_type->getShortName()], true)}">
    {$router->getUrl('shop-front-onlinepay', [Act=>fail, PaymentType=>$payment_type->getShortName()], true)}
</a>


