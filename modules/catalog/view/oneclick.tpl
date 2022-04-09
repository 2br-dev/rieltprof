{* Покупка в 1 клик *}
{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Купить в 1 клик{/t}{/block}
{block "body"}
    {$catalog_config = $this_controller->getModuleConfig()}
    {$offer_fields = $product.offer_fields}
    {if $success}
        {hook name="catalog-oneclick:success" title="{t}Купить в один клик:Успешное сообщение{/t}"}
        {t}Ваша заявка принята. В ближайшее время мы с вами свяжемся.{/t}
        {/hook}
    {else}
        {if $errors = $click->getNonFormErrors()}
            <div class="alert alert-danger">
                {$errors|join:", "}
            </div>
        {/if}

        <form enctype="multipart/form-data" method="POST" action="{$router->getUrl('catalog-front-oneclick', ["product_id"=>$product.id])}">
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="product_name" value="{$product.title}"/>
            <input type="hidden" name="offer_id" value="{$offer_fields.offer_id}">
            {hook name="catalog-oneclick:form" title="{t}Купить в один клик:форма{/t}"}
                <div class="mb-lg-5 mb-4">{t}Оставьте ваши данные и консультант с вами свяжется для оформления заказа.{/t}</div>
                <div class="modal-item mb-4">
                    <div>{$product.title}</div>
                    {if $offer_fields.multioffer || $offer_fields.offer}
                        <div class="row mt-2 g-2 align-items-center">
                            <div class="col">
                                <div class="cart-equipments fs-5">
                                    {if $product->isMultiOffersUse()}
                                        {$offers_levels = $product.multioffers.levels}
                                        {foreach $offers_levels as $level}
                                            <div class="d-flex align-items-center">
                                                <div class="text-gray">{$level.title|default:$level.prop_title}:</div>
                                                <div class="ms-1">
                                                    <input name="multioffers[{$level.prop_id}]" value="{$offer_fields.multioffer[$level.prop_id]}" readonly type="hidden">
                                                    <span>{$offer_fields.multioffer[$level.prop_id]}</span>
                                                </div>
                                            </div>
                                        {/foreach}
                                    {elseif $product->isOffersUse()}
                                        <div class="d-flex align-items-center">
                                            <div class="text-gray">{$product.offer_caption|default:"{t}Комплектация{/t}"}</div>
                                            <div class="ms-1">
                                                <input name="offer" value="{$offer_fields.offer}" readonly type="hidden">
                                                <span>{$offer_fields.offer}</span>
                                            </div>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>

                <div class="g-4 row row-cols-1">
                    <div>
                        <label class="form-label">{t}Имя{/t}</label>
                        {$click->getPropertyView('user_fio')}
                    </div>
                    <div>
                        <label class="form-label">{t}Телефон{/t}</label>
                        {$click->getPropertyView('user_phone')}
                    </div>
                    {$fld_manager = $click->getFieldsManager()}
                    {foreach $fld_manager->getStructure() as $fld}
                        <div>
                            <label class="form-label">{$fld.title}</label>
                            {$fld_manager->getForm($fld.alias, '%THEME%/helper/forms/userfields_forms.tpl')}

                            {$errname = $fld_manager->getErrorForm($fld.alias)}
                            {$error = $click->getErrorsByForm($errname, ', ')}
                            {if !empty($error)}
                                <span class="invalid-feedback">{$error}</span>
                            {/if}
                        </div>
                    {/foreach}
                    {if !$is_auth}
                        <div>
                            <label class="form-label">{$click->__kaptcha->getTypeObject()->getFieldTitle()}</label>
                            {$click->getPropertyView('kaptcha')}
                        </div>
                    {/if}
                    {if $CONFIG.enable_agreement_personal_data}
                        {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Купить{/t}"}
                    {/if}
                    <div>
                        <button type="submit" class="btn btn-primary w-100">{t}Купить{/t}</button>
                    </div>
                </div>
            {/hook}
        </form>
    {/if}
{/block}