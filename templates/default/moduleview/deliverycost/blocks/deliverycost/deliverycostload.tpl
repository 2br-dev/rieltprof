{addjs file="jquery.ui/jquery.autocomplete.js" basepath="common"}
{addjs file="%deliverycost%/deliverycost.js"}
{addcss file="%deliverycost%/deliverycost.css"}
<div id="deliveryCostInputDiv" class="deliveryCostBlock"
     data-url="{$router->getUrl('deliverycost-block-deliverycost', ['redirect' => urlencode($smarty.server.REQUEST_URI)])}"
     data-block-id="{$_block_id}">

    <p class="deliveryCostListTitle">{t}Стоимость доставки в{/t} <b>{t}{$city.title}{/t}</b> (<a data-href="{$router->getUrl('deliverycost-front-choosecityautocomplete', ['redirect' => urlencode($smarty.server.REQUEST_URI)])}" class="inDialog">{t}Другой город{/t}</a>)</p>
    <button class="deliveryCostCalculateButton">Рассчитать</button>
    <div class="deliveryLoadScreen">
        {t}Подождите идёт расчёт доставки...{/t}
    </div>
</div>

<script>
    $(document).on('click', '.deliveryCostCalculateButton',function(){
        $(this).css('display', 'none');
        $('.deliveryLoadScreen').css('display', 'block');
        $('.deliveryCostBlock').deliveryCost();
        $('.deliveryCostBlock').deliveryCost('queryDeliveryBlock');
    });
</script>