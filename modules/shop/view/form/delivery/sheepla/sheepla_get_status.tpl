{addjs file="$public_api_js_url" basepath="root"}
{addcss file="$public_api_css_url" basepath="root"}
<div class="titlebox">{$title}</div>
 
{$app->autoloadScripsAjaxBefore()}
<div id="areaSheepla" class="{if $type=="standard" || $type=="short"}sheeplaStatusDivStandart{else}sheeplaStatusDiv{/if}">

</div>

<script type="text/javascript">

$.allReady(function(){
    sheepla.init({
        apikey: '{$api_key}', 
        cultureId: '{$cultureId}' 
    });       
    sheepla.get_shipment_status_by_order_id({$order.order_num}, '#areaSheepla','{$type}', 1, 1);  
});


</script>          
{$app->autoloadScripsAjaxAfter()}