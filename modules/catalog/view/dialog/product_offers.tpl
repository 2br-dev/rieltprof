{foreach $product->getOffers() as $offer}
    <tr class="product-item-offer">
        <td class="chk"></td>
        <td></td>
        <td class="image"></td>
        <td class="title">{$offer.title}</td>
        <td class="no"></td>
        <td class="barcode textright">{$product->getBarcode($offer.id)}</td>
        <td class="barcode textright">{$product->getNum($offer.id)}</td>
        <td class="barcode textright">{$product->getSku($offer.id)}</td>
    </tr>
{/foreach}