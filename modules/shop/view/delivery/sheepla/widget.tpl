{if !$template_id}
    <p>{t}Укажите шаблон sheepla{/t}</p>
{else}
    
    {addjs file="http://code.jquery.com/jquery-migrate-1.2.0.js" basepath="root"}
    {addjs file="$public_api_js_url" basepath="root"}
    {addjs file="{$mod_js}/delivery/sheepla_widjet.js" basepath="root"}
    {addcss file="$public_api_css_url" basepath="root"}

    <div id="sheeplaMap{$delivery.id}" class="sheeplaMap" data-sheepla-info='{ "apikey":"{$public_api_key}", "deliveryId":"{$delivery.id}", "cultureId":"{$cultural_id}", "templateId":"{$template_id}", "divId":"#sheeplaMap{$delivery.id}", "userEmail":"{$user.e_mail}", "userPhone":"{$user.phone}", "inputId":"#sheeplaInputMap{$delivery.id}" }' data-input-id="#sheeplaInputMap{$delivery.id}" data-delivery-id="{$delivery.id}">
        
    </div>

    <input id="sheeplaInputMap{$delivery.id}" type="hidden" name="delivery_extra[value]" value="" disabled="disabled"/>
{/if}