{addcss file="%catalog%/selectproduct.css"}
{addjs file="%catalog%/selectproduct.js"}
{addcss file="%pushsender%/pushtokenmessage.css"}
{addjs file="%pushsender%/pushtokenmessage.js"}

<div id="pushTokenMessageWrapper" class="pushTokenMessageWrapper hide-group-cb hide-product-cb show-virtual-dirs" data-urls='{ "getChild": "{adminUrl mod_controller="catalog-dialog" do="getChildCategory"}", "getProducts": "{adminUrl mod_controller="catalog-dialog" do="getProducts"}", "getDialog": "{adminUrl mod_controller="catalog-dialog" do=false}" }'>
    
    <div id="messageType">
        {$elem.__mobile_banner_type->formView()}
    </div>

    <div class="typeContainer">
        <div id="messageLink" class="ptmType">
            {include file="%pushsender%/form/banner/link.tpl"}
        </div>

        <div id="messageMenu" class="ptmType">
            {include file="%pushsender%/form/banner/menu_id.tpl"}
        </div>

        <div id="messageProduct" class="ptmType">
            {include file="%pushsender%/form/banner/product_id.tpl"}
        </div>

        <div id="messageCategory" class="ptmType">
            {include file="%pushsender%/form/banner/category_id.tpl"}
        </div>
    </div>
</div>

<script>
    $.allReady(function() {
       $("#pushTokenMessageWrapper").pushTokenMessage({
           defaultSelect: "Menu"
       });
    });
</script>