{* Шаблон диалога покупки корзины в 1 клик *}

{if !$url->request('dialogWrap', $smarty.const.TYPE_INTEGER)}
    {* Отображаем, если идет вставка блока на странице корзины *}
    <div class="d-flex justify-content-center mt-4">
        <a class="product-one-click{if $param.disabled} disabled{/if} rs-in-dialog" data-href="{$router->getUrl('shop-block-oneclickcart', [_block_id => $_block_id])}">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
                <path d="M18.7 7.94101L15.2818 4.18466C14.6054 3.5083 13.5036 3.5074 12.8209 4.19012C12.6045 4.40647 12.4573 4.66556 12.3809 4.94195C11.7009 4.42104 10.72 4.47286 10.0936 5.09924C9.87817 5.31559 9.7309 5.57468 9.6527 5.85013C8.97271 5.33106 7.99179 5.38194 7.36635 6.00831C7.15452 6.22015 7.00908 6.47285 6.92999 6.74195L5.31906 5.13103C4.6427 4.45466 3.54088 4.45377 2.85816 5.13648C2.1809 5.81285 2.1809 6.91466 2.85816 7.59192L9.04725 13.781L5.37633 14.3574C4.3845 14.4992 3.63633 15.361 3.63633 16.3637C3.63633 17.1156 4.24814 17.7274 4.99997 17.7274H13.5718C14.9073 17.7274 16.1627 17.2074 17.1072 16.2628L18.5372 14.8328C19.4809 13.8901 19.9999 12.6355 19.9999 11.3019C19.9999 10.0564 19.5381 8.86283 18.7 7.94101ZM17.8945 14.1901L16.4645 15.6201C15.6918 16.3928 14.6645 16.8183 13.5717 16.8183H4.99993C4.74902 16.8183 4.54537 16.6146 4.54537 16.3637C4.54537 15.8109 4.95808 15.3355 5.51172 15.2564L10.0708 14.54C10.2399 14.5137 10.379 14.3946 10.4327 14.2319C10.4854 14.0701 10.4427 13.8909 10.3217 13.7701L3.50086 6.94918C3.17815 6.62647 3.17815 6.101 3.50542 5.77283C3.66723 5.61192 3.87906 5.531 4.09089 5.531C4.30272 5.531 4.51451 5.61192 4.67636 5.77373L9.22456 10.3219C9.40183 10.4992 9.69003 10.4992 9.8673 10.3219C9.95547 10.2328 10 10.1165 10 10.0001C10 9.88373 9.95551 9.76739 9.86641 9.67829L8.00913 7.82102C7.68642 7.4983 7.68642 6.97373 8.01459 6.64556C8.33641 6.32374 8.86277 6.32374 9.18459 6.64556L11.0428 8.50373C11.22 8.681 11.5082 8.681 11.6855 8.50373C11.7737 8.41462 11.8182 8.29829 11.8182 8.18191C11.8182 8.06553 11.7737 7.9492 11.6846 7.86009L10.7364 6.9119C10.4137 6.58918 10.4137 6.06461 10.7419 5.73644C11.0637 5.41462 11.59 5.41462 11.9119 5.73644L12.8646 6.68915C12.8664 6.69005 12.8655 6.69005 12.8655 6.69005L12.8664 6.69094C12.8673 6.69184 12.8673 6.69184 12.8673 6.69184C12.8682 6.69273 12.8682 6.69273 12.8682 6.69273H12.8691C12.8699 6.69273 12.8699 6.69363 12.8699 6.69363C13.0481 6.86272 13.33 6.8591 13.5036 6.68455C13.6808 6.50728 13.6808 6.21908 13.5036 6.04181L13.4645 6.00273C13.3081 5.84638 13.2218 5.63817 13.2218 5.41726C13.2218 5.19635 13.3072 4.98908 13.4691 4.82728C13.7927 4.50546 14.3163 4.50635 14.6245 4.81181L18.0282 8.55273C18.7127 9.30644 19.0909 10.2828 19.0909 11.3019C19.0909 12.3928 18.6663 13.4183 17.8945 14.1901Z"
                />
                <path d="M7.81454 4.68431C7.15275 3.21976 5.69093 2.27246 4.09091 2.27246C1.83546 2.27246 0 4.10792 0 6.36337C0 7.96339 0.947259 9.42521 2.41181 10.0879C2.4727 10.1152 2.53637 10.128 2.59909 10.128C2.77181 10.128 2.93727 10.0279 3.01364 9.86068C3.11638 9.63159 3.01453 9.36248 2.78638 9.25884C1.64638 8.74339 0.90912 7.60701 0.90912 6.36337C0.90912 4.60884 2.33638 3.18154 4.09095 3.18154C5.33459 3.18154 6.47097 3.9188 6.98642 5.0588C7.08917 5.28789 7.35917 5.39063 7.58732 5.28606C7.81637 5.18246 7.91817 4.9134 7.81454 4.68431Z"/>
            </svg>
            <span class="ms-2">{t}Купить в 1 клик{/t}</span>
        </a>
    </div>
{else}
    {$catalog_config = $this_controller->getModuleConfig()}
    {$offer_fields = $product.offer_fields}
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title h2">{t}Купить в 1 клик{/t}</div>
                <button type="button" class="btn-close modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="{$THEME_IMG}/icons/close.svg" width="24" height="24" alt="">
                </button>
            </div>
            <div class="modal-body">
                {if $success}
                    {hook name="shop-oneclickcart:success" title="{t}Купить в один клик корзину:Успешное сообщение{/t}"}
                        {t}Ваша заявка принята. В ближайшее время мы с вами свяжемся.{/t}
                    {/hook}
                {else}
                    {if $errors = $click->getNonFormErrors()}
                        <div class="alert alert-danger">
                            {$errors|join:", "}
                        </div>
                    {/if}
                    <form enctype="multipart/form-data" method="POST" action="{$router->getUrl('shop-block-oneclickcart')}">
                        {$this_controller->myBlockIdInput()}
                        {hook name="shop-oneclickcart:form" title="{t}Купить в один клик корзину:Форма{/t}"}
                            <div class="mb-lg-5 mb-4">{t}Оставьте ваши данные и консультант с вами свяжется для оформления заказа.{/t}</div>
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
                                {if !$is_auth && $use_captcha && ModuleManager::staticModuleEnabled('kaptcha')}
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
            </div>
        </div>
    </div>
{/if}
