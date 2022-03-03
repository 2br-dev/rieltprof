{strip}
{if count($data->oneclick.products)==1}{* Если только один товар *}
    {$first_product=current($data->oneclick.products)}
    {$offers_info = $first_product.offer_fields}
    {if !empty($offers_info.barcode)}{$barcode = $offers_info.barcode}{else}{$barcode = $first_product.barcode}{/if}
    {t d=$barcode}Ваш заказ на товар с артикулом №%d принят.{/t}
{else}
    {t}Ваш заказ принят. Скоро с Вами свяжутся.{/t}
{/if}
{/strip}