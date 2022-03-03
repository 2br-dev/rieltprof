{t alias="2can, настройка аккаунта 2can"}Зайдите в ваш аккаунт на сайте 2can.ru, перейдите в раздел Настройки -> Настройки уведомлений.

Включите уведомления, укажите следующий URL в поле <b>Адреc для HTTP-запросов</b>{/t}:<br>
https://{$Setup.DOMAIN}{$router->getUrl('shop-front-onlinepay', ["Act" => "result", "secret_shop" => $payment_type->getShopSecret(), "PaymentType"=>$payment_type->getShortName()])}