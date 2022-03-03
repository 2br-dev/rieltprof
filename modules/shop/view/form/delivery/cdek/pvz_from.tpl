{$app->autoloadScripsAjaxBefore()}
{addjs file="%shop%/delivery/rs.field_pvz.js"}

{$type_object = $elem->getParentObject()}
{$delivery = $type_object->getDelivery()}

{if !empty($delivery['id'])}
    <div class="field-pvz-from field-pvz-line rs-field-pvz" data-pvz-select-url="{$router->getUrl('shop-front-selectpvz', [], true)}" data-field-pvz-options='{json_encode(['deliveryId' => $delivery['id'], 'cityIdSelector' => '[name="data[city_from]"]'])}'>
        <div class="rs-field-pvz-label">
            {$pvz_from = $type_object->api->getPvzFrom()}
            {if $pvz_from}
                {$pvz_from->getPickPointTitle()}
            {else}
                {t}Не указан{/t}
            {/if}
        </div>
        <a class="btn btn-sm btn-primary btn-alt rs-field-pvz-select">{t}Изменить{/t}</a>
        <input type="hidden" name="{$field->getFormName()}" class="rs-field-pvz-input" value="{$field->get()}">
    </div>
{else}
    {t}Выбор ПВЗ доступен только после сохранения объекта{/t}
{/if}
{$app->autoloadScripsAjaxAfter()}