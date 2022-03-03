{addjs file="jquery.tablednd/jquery.tablednd.js" basepath="common"}
{addcss file="%catalog%/offer.css?v=6" basepath="root"}
{addjs file="%catalog%/offer.js?v=4" basepath="root"}
{addjs file="tmpl/tmpl.min.js" basepath="common"}

<div id="offers" data-urls='{ "offerEdit": "{$router->getAdminUrl(false, ['odo' => 'offerEdit', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerChangeWithMain": "{$router->getAdminUrl(false, ['odo' => 'offerChangeWithMain', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerDelete": "{$router->getAdminUrl(false, ['odo' => 'offerdelete', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerMultiEdit": "{$router->getAdminUrl(false, ['odo' => 'offermultiedit', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerMakeFromMultioffer": "{$router->getAdminUrl(false, ['odo' => 'OfferMakeFromMultioffers', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerLinkPhoto": "{$router->getAdminUrl(false, ['odo' => 'OfferLinkPhoto', 'product_id' => $elem.id], 'catalog-block-offerblock')}",
                              "offerLinkPhotoSave": "{$router->getAdminUrl(false, ['odo' => 'OfferLinkPhotoSave', 'product_id' => $elem.id], 'catalog-block-offerblock')}" }'>
    {include file="%catalog%/adminblocks/offerblock/multioffers.tpl"}
    
    <div class="offer-block">
        <table class="otable">
           <tbody>
               <tr>
                    <td class="title" width="200">{t}Подпись к комплектациям{/t}:</td>
                    <td>{include file=$elem.__offer_caption->getRenderTemplate() field=$elem.__offer_caption}</td>
               </tr>
           </tbody>
        </table>
        
        <div id="all-offers">
            {include file="%catalog%/adminblocks/offerblock/offer_all.tpl"}        
        </div>
    </div>
</div>

<script type=" text/javascript">
    $.allReady(function() {
        $('#offers').offer();
    });
</script>