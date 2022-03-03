{$item = $cell->getRow()}
{$amount = $item['items_count']}
<a href="{$router->getAdminUrl('edit', ['id' => $cell->getRow()->id], 'catalog-inventoryarrivalctrl')}" class="crud-edit">{t n=$amount}Оприходование %n [plural:%n:товара|товаров|товаров]{/t}</a>