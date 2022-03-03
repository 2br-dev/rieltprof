{* Оформление заказа. Шаг - Адрес *}
{assign var=shop_config value=ConfigLoader::byModule('shop')}
{addjs file="rs.order.js"}

<div class="page-registration-steps">
    <div class="t-registration-steps">

            {* Текущий шаг оформления заказа *}
            {moduleinsert name="\Shop\Controller\Block\CheckoutStep"}

            <div class="form-style">
                <form method="POST" class="t-order rs-order-form {$order.user_type|default:"authorized"}" data-city-autocomplete-url="{$router->getUrl('shop-front-checkout', ['Act'=>'searchcity'])}">

                    {if $errors=$order->getNonFormErrors()}
                        <div class="page-error">
                            {foreach $errors as $item}
                                <p>{$item}</p>
                            {/foreach}
                        </div>
                    {/if}

                    {if !$is_auth}
                    <ul class="nav nav-tabs hidden-xs hidden-sm rs-user-type-tabs">
                        <li {if $order.user_type == 'person'}class="active"{/if}><a data-toggle="tab" data-value="person">{t}Частное лицо{/t}</a></li>
                        <li {if $order.user_type == 'company'}class="active"{/if}><a data-toggle="tab" data-value="company">{t}Компания{/t}</a></li>
                        <li {if $order.user_type == 'noregister'}class="active"{/if}><a data-toggle="tab" data-value="noregister">{t}Без регистрации{/t}</a></li>
                    </ul>
                    {/if}

                    <div class="t-order_contact-information user-contacts">
                        <h3 class="h3">{t}Контактные данные{/t}</h3>

                        {if !$is_auth}
                            <div class="form-group visible-xs visible-sm">
                                <input type="radio" id="type-user" name="user_type" value="person" {if $order.user_type=='person'}checked{/if}> <label for="type-user">{t}Частное лицо{/t}</label><br>
                                <input type="radio" id="type-company" name="user_type" value="company" {if $order.user_type=='company'}checked{/if}> <label for="type-company">{t}Компания{/t}</label><br>
                                <input type="radio" id="type-noregister" name="user_type" value="noregister" {if $order.user_type=='noregister'}checked{/if}> <label for="type-noregister">{t}Без регистрации{/t}</label><br>
                            </div>
                        {/if}

                        {if $is_auth}
                            <table class="table-underlined">
                                {if $user.is_company}
                                    <tr class="table-underlined-text">
                                        <td><span>{t}Наименование компании{/t}</span></td>
                                        <td><span>{$user.company}</span></td>
                                    </tr>
                                    <tr class="table-underlined-text">
                                        <td><span>{t}ИНН{/t}</span></td>
                                        <td><span>{$user.company_inn}</span></td>
                                    </tr>
                                {/if}

                                {foreach ['name','surname', 'midname', 'phone', 'e_mail'] as $field}
                                    {if $user[$field] != ''}
                                        <tr class="table-underlined-text">
                                            <td>{$user->getProp($field)->getDescription()}</td>
                                            <td>{$user[$field]}</td>
                                        </tr>
                                    {/if}
                                {/foreach}

                            </table>
                            <div class="form-group changeUser">
                                <a href="{urlmake logout=true}" class="link link-white">{t}Сменить пользователя{/t}</a>
                            </div>
                        {else}
                            <div class="user-authorization-info">
                                {t url=$user_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="inDialog">авторизуйтесь</a>.{/t}
                            </div>

                            <div class="user-register">
                                <div class="organization">
                                    <div class="form-group">
                                        <label class="label-sup">{t}Наименование компании{/t}</label>
                                        {$order->getPropertyView('reg_company', ['placeholder' => "{t}Например: ООО Ромашка{/t}"])}
                                    </div>
                                    <div class="form-group">
                                        <label class="label-sup">{t}ИНН{/t}</label>
                                        {$order->getPropertyView('reg_company_inn', ['placeholder' => "{t}10 или 12 цифр{/t}"])}
                                    </div>
                                </div>
                                {if $user_config.user_one_fio_field}
                                    <div class="form-group">
                                        <label class="label-sup">{t}Ф.И.О.{/t}</label>
                                        {$order->getPropertyView('reg_fio', ['placeholder' => "{t}Например, Иванов Иван Иванович{/t}"])}
                                    </div>
                                {else}
                                    {if $user_config->canShowField('name')}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Имя{/t}</label>
                                            {$order->getPropertyView('reg_name', ['placeholder' => "{t}Имя покупателя, владельца аккаунта{/t}"])}
                                        </div>
                                    {/if}
                                    {if $user_config->canShowField('surname')}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Фамилия{/t}</label>
                                            {$order->getPropertyView('reg_surname', ['placeholder' => "{t}Фамилия покупателя, владельца аккаунта{/t}"])}
                                        </div>
                                    {/if}
                                    {if $user_config->canShowField('midname')}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Отчество{/t}</label>
                                            {$order->getPropertyView('reg_midname', ['placeholder' => "{t}Отчество покупателя, владельца аккаунта{/t}"])}
                                        </div>
                                    {/if}
                                {/if}

                                {if $user_config->canShowField('phone')}
                                    <div class="form-group">
                                        <label class="label-sup">{t}Телефон{/t}</label>
                                        {$order->getPropertyView('reg_phone', ['placeholder' => "{t}В формате: +7(123)9876543{/t}"])}
                                    </div>
                                {/if}

                                {if $user_config->canShowField('login')}
                                    <div class="form-group">
                                        <label class="label-sup">{t}Телефон{/t}</label>
                                        {$order->getPropertyView('reg_login', ['placeholder' => "{t}Придумайте логин для входа{/t}"])}
                                    </div>
                                {/if}

                                {if $user_config->canShowField('e_mail')}
                                    <div class="form-group">
                                        <label class="label-sup">{t}E-mail{/t}</label>
                                        {$order->getPropertyView('reg_e_mail')}
                                    </div>
                                {/if}

                                <div class="form-group">
                                    <label class="label-sup">{t}Пароль{/t}</label>

                                    <input type="checkbox" name="reg_autologin" {if $order.reg_autologin}checked{/if} value="1" id="reg-autologin">&nbsp;<label for="reg-autologin">{t}Получить автоматически на e-mail или телефон{/t}</label>
                                    <div class="help">{t}Нужен для проверки статуса заказа, обращения в поддержку, входа в кабинет{/t}</div>

                                    <div class="rs-manual-login" {if $order.reg_autologin}style="display:none"{/if}>
                                        <div class="inline">
                                            {$order->getPropertyView('reg_openpass', ['placeholder' => "{t}Пароль{/t}"])}
                                        </div>
                                        <div class="inline">
                                            {$order->getPropertyView('reg_pass2', ['placeholder' => "{t}Повтор пароля{/t}"])}
                                        </div>
                                    </div>
                                </div>

                                {foreach $reg_userfields->getStructure() as $fld}
                                    <div class="form-group">
                                        <label class="label-sup">{$fld.title}</label>
                                        {$reg_userfields->getForm($fld.alias)}
                                        {$errname=$reg_userfields->getErrorForm($fld.alias)}
                                        {$error=$order->getErrorsByForm({$errname}, ', ')}
                                        {if !empty($error)}
                                            <span class="formFieldError">{$error}</span>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>
                        {/if}

                        <div class="user-without-register">
                            <div class="form-group">
                                <label class="label-sup">{t}Ф.И.О.{/t}</label>
                                {$order->getPropertyView('user_fio', ['placeholder' => "{t}Фамилия, Имя и Отчество покупателя, владельца аккаунта{/t}"])}
                            </div>
                            <div class="form-group">
                                <label class="label-sup">{t}E-mail{/t}</label>
                                {$order->getPropertyView('user_email', ['placeholder' => "{t}E-mail покупателя, владельца аккаунта{/t}"])}
                            </div>
                            <div class="form-group">
                                <label class="label-sup">{t}Телефон{/t}</label>
                                {$order->getPropertyView('user_phone', ['placeholder' => "{t}В формате: +7(123)9876543{/t}"])}
                            </div>
                        </div>

                        {if $order->__code->isEnabled()}
                            <div class="form-group captcha">
                                <label class="label-sup">{$order->__code->getTypeObject()->getFieldTitle()}</label>
                                {$order->getPropertyView('code')}
                            </div>
                        {/if}

                    </div>

                    {if $have_to_address_delivery && $shop_config->isCanShowAddress()}
                        <div class="t-order_contact-information">
                            <h3 class="h3">{t}Адрес{/t}</h3>
                            
                            {if $have_pickup_points}
                                <div class="formPickUpTypeWrapper">
                                    <input id="onlyPickUpPoints" type="radio" name="only_pickup_points" value="1" {if $order.only_pickup_points}checked{/if}/> <label for="onlyPickUpPoints">{t}Самовывоз{/t}</label><br/>
                                    <input id="onlyDelivery" type="radio" name="only_pickup_points" value="0" {if !$order.only_pickup_points}checked{/if}/> <label for="onlyDelivery">{t}Доставка по адресу{/t}</label>
                                </div>
                            {/if}

                            <div id="form-address-section-wrapper" class="{if $have_pickup_points && $order.only_pickup_points}hidden{/if}">
                                {if empty($address_list)}
                                    {$count_list = 0}
                                {else}
                                    {$count_list = count($address_list)}
                                {/if}
                                {if $count_list>0}
                                    <ul class="form-group last-address rs-last-address">
                                        {foreach $address_list as $address}
                                            <li class="item">
                                                <input type="radio" name="use_addr" value="{$address.id}" id="adr_{$address.id}" {if $order.use_addr == $address.id}checked{/if}><label for="adr_{$address.id}">{$address->getLineView()}</label>
                                                <a href="{$router->getUrl('shop-front-checkout', ['Act' =>'deleteAddress', 'id' => $address.id])}" class="rs-delete-address"><i class="pe-2x pe-va pe-7s-close"></i></a>
                                            </li>
                                        {/foreach}
                                        <li>
                                            <input type="radio" name="use_addr" value="0" id="use_addr_new" {if $order.use_addr == 0}checked{/if}><label for="use_addr_new">{t}Другой адрес{/t}</label>
                                        </li>
                                    </ul>
                                {else}
                                    <input type="hidden" name="use_addr" value="0">
                                {/if}

                                <div class="rs-new-address{if $order.use_addr>0 && $address_list} hide{/if}">
                                    {if $shop_config.require_country}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Страна{/t}</label>
                                            {$region_tools_url=$router->getUrl('shop-front-regiontools', ["Act" => 'listByParent'])}
                                            {$order->getPropertyView('addr_country_id', ['data-region-url' => $region_tools_url])}
                                        </div>
                                    {/if}
                                    {if $shop_config.require_region}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Область/край{/t}</label>
                                            {$regcount=$order->regionList()}
                                            <span {if count($regcount) == 0}style="display:none"{/if} id="region-select">
                                                {$order.__addr_region_id->formView()}
                                            </span>

                                            <span {if count($regcount) > 0}style="display:none"{/if} id="region-input">
                                                {$order.__addr_region->formView()}
                                            </span>
                                        </div>
                                    {/if}
                                    {if $shop_config.require_city}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Город{/t}</label>
                                            {$order->getPropertyView('addr_city')}
                                        </div>
                                    {/if}
                                    {if $shop_config.require_zipcode}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Индекс{/t}</label>
                                            {$order.__addr_zipcode->formView()}
                                        </div>
                                    {/if}
                                    {if $shop_config.require_address}
                                        <div class="form-group">
                                            <label class="label-sup">{t}Адрес{/t}</label>
                                            {$order->getPropertyView('addr_address')}
                                        </div>
                                    {/if}
                                </div>

                                {if $shop_config.show_contact_person}
                                    <div class="form-group">
                                        <label class="label-sup">{t}Контактное лицо{/t}</label>
                                        {$order->getPropertyView('contact_person', ['placeholder' => "{t}Лицо, которое встретит доставку. Например: Иван Иванович Пуговкин{/t}"])}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    {else}
                        <input type="hidden" name="only_pickup_points" value="1"/>
                    {/if}
                    
                    {if $conf_userfields->notEmpty()}
                        <div class="t-order_contact-information">
                            <div class="additional">
                                <h3 class="h3">{t}Дополнительные сведения{/t}</h3>
                                {foreach $conf_userfields->getStructure() as $fld}
                                    <div class="form-group">
                                        <label class="label-sup">{$fld.title}</label>
                                        {$conf_userfields->getForm($fld.alias)}
                                        {$errname=$conf_userfields->getErrorForm($fld.alias)}
                                        {$error=$order->getErrorsByForm($errname, ', ')}
                                        {if !empty($error)}
                                            <span class="formFieldError">{$error}</span>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    {/if}
                    
                    {if $CONFIG.enable_agreement_personal_data}
                        <div class="t-order_contact-information">
                            {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Далее{/t}"}
                        </div>
                    {/if}
                    <div class="form__menu_buttons text-center next">
                        <button type="submit" class="link link-more">{t}Далее{/t}</button>
                    </div>
                </form>
            </div>

    </div>
</div>