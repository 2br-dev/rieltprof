{$product = $cell->getRow()}
{$table = $cell->getContainer()}
{if count($product->getOffers()) > 1}
    <i class="offer-block-toggle zmdi" data-id="{$product.id}" data-url-load-offers="{adminUrl do="getOffersTableData" product_id=$product.id}"></i>
{/if}