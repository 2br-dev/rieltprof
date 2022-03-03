{$app->autoloadScripsAjaxBefore()}
{addjs file="//api-maps.yandex.ru/2.1/?lang=ru_RU" basepath="root"}
{addjs file="%shop%/delivery/widjet.js"}
{addcss file="%shop%/delivery/widjet.css"}

<div id="deliveryWidjet{$delivery.id}" class="deliveryWidjet" data-delivery-id="{$delivery.id}">
    {if empty($errors)}
        {$wrapped_content}
    {/if}
</div>
{$app->autoloadScripsAjaxAfter()}