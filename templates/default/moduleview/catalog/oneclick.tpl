{assign var=catalog_config value=$this_controller->getModuleConfig()} 
<div class="oneClickWrapper">
    {if $success}
        <div class="reserveForm">
            {hook name="catalog-oneclick:success" title="{t}Купить в один клик:Успешное сообщение{/t}"}
            <h2 class="dialogTitle" data-dialog-options='{ "width": "400" }'>{t}Заказ принят{/t}</h2>
            <p class="prodtitle">{$product.title} {t}Артикул:{/t}{$product.barcode}</p>
            <p class="infotext">
                {t}В ближайшее время с Вами свяжется наш менеджер.{/t}
            </p>
            {/hook}
        </div>
    {else}
        <form enctype="multipart/form-data" class="reserveForm" action="{$router->getUrl('catalog-front-oneclick',["product_id"=>$product.id])}" method="POST"> 
            {$this_controller->myBlockIdInput()} 
            <input type="hidden" name="product_name" value="{$product.title}"/>
            <input type="hidden" name="offer_id" value="{$offer_fields.offer_id}">
            {hook name="catalog-oneclick:form" title="{t}Купить в один клик:форма{/t}"}  
            <h2 class="dialogTitle" data-dialog-options='{ "width": "400" }'>{t}Купить в один клик{/t}</h2>
            <p class="infotext">
                 {t}Оставьте Ваши данные и наш консультант с вами свяжется.{/t}
            </p>  
            {if $error_fields}
               <div class="pageError"> 
               {foreach from=$error_fields item=error_field}
                   {foreach from=$error_field item=error}
                        <p>{$error}</p>
                   {/foreach}
               {/foreach}
               </div>
            {/if}
           
            <table class="formTable tabFrame">
                {if $product->isMultiOffersUse()}
                    <tr>
                        <td class="key">{$product.offer_caption|default:t('Комплектация')}</td>
                        <td class="value">
                        </td>
                    </tr>
                    {assign var=offers_levels value=$product.multioffers.levels} 
                    {foreach $offers_levels as $level}
                        <tr>
                            <td class="key">{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</td>
                            <td class="value">
                                <input name="multioffers[{$level.prop_id}]" value="{$offer_fields.multioffer[$level.prop_id]}" readonly>
                            </td>
                        </tr>
                    {/foreach}
                {elseif $product->isOffersUse()}
                    {assign var=offers value=$product.offers.items}
                    <tr>
                        <td class="key">{$product.offer_caption|default:t('Комплектация')}</td>
                        <td class="value">
                            <input name="offer" value="{$offer_fields.offer}" readonly>
                        </td>
                    </tr>
               {/if}
            </table>
               
            <table class="formTable tabFrame">
               <tbody>
                   <tr class="clickRow">
                        
                       <td class="key">
                          {t}Ваше имя{/t}
                       </td>
                       <td class="value">
                          <input type="text" class="inp {if isset($display_errors.name)}has-error{/if}" value="{if $request->request('name','string')}{$request->request('name','string')}{else}{$click.user_fio}{/if}" maxlength="100" name="name">
                       </td>
                   </tr>
                   <tr class="clickRow">
                       <td class="key">
                          {t}Ваш телефон{/t}
                       </td> 
                       <td class="value">
                          <input type="text" class="inp {if isset($display_errors.phone)}has-error{/if}" value="{if $request->request('phone','string')}{$request->request('phone','string')}{else}{$click.user_phone}{/if}" maxlength="20" name="phone">
                       </td>
                   </tr>
                   
                   {foreach from=$oneclick_userfields->getStructure() item=fld}
                       <tr>
                           <td class="key">{$fld.title}</td>
                           <td class="value">
                               {$oneclick_userfields->getForm($fld.alias)}                   
                           </td>
                       </tr>
                   {/foreach}
                    
                   {if !$is_auth}
                       <tr>
                           <td class="key">{$click->__kaptcha->getTypeObject()->getFieldTitle()}</td>
                           <td class="value">{$click->getPropertyView('kaptcha')}</td>
                       </tr>
                   {/if}
                   
               </tbody>
            </table>
           
           <div class="centerWrap">
               <input type="submit" value="{t}Отправить{/t}" class="formSave">
               <span class="unobtainable">{t}Нет в наличии{/t}</span>
           </div>
           {/hook}
        </form>
    {/if}
</div>