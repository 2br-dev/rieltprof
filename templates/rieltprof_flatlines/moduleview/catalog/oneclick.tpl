{$catalog_config=$this_controller->getModuleConfig()}

<div class="one-click-wrapper">
    {if $success}
        <div class="form-style modal-body reserve-form">
            <h2 class="h2">{t}Заказ принят{/t}</h2><br>
            <div>
                <h3 class="h3">{$product.title}</h3>
                <p>{t}Артикул{/t}:{$product.barcode}</p>
                <p>{t}В ближайшее время с Вами свяжется наш менеджер{/t}</p>
            </div>
        </div>    
    {else}
        <form enctype="multipart/form-data" method="POST" action="{$router->getUrl('catalog-front-oneclick',["product_id"=>$product.id])}" class="form-style modal-body">
           {$this_controller->myBlockIdInput()}
           <input type="hidden" name="product_name" value="{$product.title}"/>
           <input type="hidden" name="offer_id" value="{$offer_fields.offer_id}">
           
           {hook name="catalog-oneclick:form" title="{t}Купить в один клик:форма{/t}"} 
           <h2 class="h2">{t}Купить в один клик{/t}</h2>
           
           <p class="infotext">
               {t}Оставьте Ваши данные и наш консультант с вами свяжется.{/t}
           </p>
            
           <p class="forms">
               {if $error_fields}
                   <div class="page-error">
                   {foreach $error_fields as $error_field}
                       {foreach $error_field as $error}
                            <p>{$error}</p>
                       {/foreach}
                   {/foreach}
                   </div>
               {/if}

                  {if $product->isMultiOffersUse()}
                        <p>
                            {$product.offer_caption|default:t('Комплектация')}
                        </p>
                        {$offers_levels=$product.multioffers.levels}
                        <table class="table-underlined">
                            {foreach $offers_levels as $level}
                                <tr class="table-underlined-text">
                                    <td><span>{if $level.title}{$level.title}{else}{$level.prop_title}{/if}</span></td>
                                    <td><input name="multioffers[{$level.prop_id}]" value="{$offer_fields.multioffer[$level.prop_id]}" readonly type="hidden">
                                        <span>{$offer_fields.multioffer[$level.prop_id]}</span>
                                    </td>
                                </tr>
                            {/foreach}
                        </table>
                   {elseif $product->isOffersUse()}

                        {$offers=$product.offers.items}
                        <table class="table-underlined">
                            <tr class="table-underlined-text">
                                <td><span>{$product.offer_caption|default:t('Комплектация')}</span></td>
                                <td><input name="offer" value="{$offer_fields.offer}" readonly type="hidden">
                                    <span>{$offer_fields.offer}</span>
                                </td>
                            </tr>
                        </table>

                   {/if}
                   
                   <div class="form-group">
                        <label class="label-sup">{t}Ваше имя{/t}</label>
                        <input type="text" class="inp {if $error_fields}has-error{/if}" value="{if $request->request('name','string')}{$request->request('name','string')}{else}{$click.user_fio}{/if}" maxlength="100" name="name">
                    </div>
                    <div class="form-group">
                        <label class="label-sup">{t}Телефон{/t}</label>
                        <input type="text" class="inp {if $error_fields}has-error{/if}" value="{if $request->request('phone','string')}{$request->request('phone','string')}{else}{$click.user_phone}{/if}" maxlength="20" name="phone">
                    </div>
                   {foreach $oneclick_userfields->getStructure() as $fld}
                   <div class="form-group">
                        <label class="label-sup">{$fld.title}</label>
                        {$oneclick_userfields->getForm($fld.alias)}
                    </div>
                    {/foreach}
                    {if !$is_auth}
                    <div class="form-group captcha">
                        <label class="label-sup">{$click->__kaptcha->getTypeObject()->getFieldTitle()}</label>
                        {$click->getPropertyView('kaptcha')}
                    </div>
                   {/if}
                        {if $CONFIG.enable_agreement_personal_data}
                            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Купить{/t}"}
                        {/if}
                   <div class="form__menu_buttons mobile-center">

                        <button type="submit" class="link link-more">{t}Купить{/t}</button>
                        <span class="rs-unobtainable">{t}Нет в наличии{/t}</span>
                   </div>
               </div>
           {/hook}
        </form>
    {/if}
</div>