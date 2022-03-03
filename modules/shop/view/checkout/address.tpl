{assign var=shop_config value=ConfigLoader::byModule('shop')}
{addjs file="order.js"}

{$errors=$order->getNonFormErrors()}
{if $errors}
    <div class="pageError">
        {foreach from=$errors item=item}
            <p>{$item}</p>
        {/foreach}
    </div>
{/if}

<form method="POST" id="order-form" data-city-autocomplete-url="{$router->getUrl('shop-front-checkout', ['Act'=>'searchcity'])}">
    {if $is_auth}
        <div class="formSection">
            <span class="formSectionTitle">{t}Покупатель{/t}</span>
            <a href="{urlmake logout=true}" class="ml10">{t}сменить пользователя (Выход){/t}</a>
        </div>
        
        <table class="formTable">
            {if $user.is_company}
            <tbody>
                <tr>
                    <td class="key">{t}Название организации{/t}:</td>
                    <td class="value">
                        {$user.company}
                    </td>
                </tr>
                <tr>
                    <td class="key">{t}ИНН{/t}:</td>
                    <td class="value">
                        {$user.company_inn}
                    </td>
                </tr>
            </tbody>
            {/if}
            <tbody>
                {foreach ['name','surname', 'midname', 'phone', 'e_mail'] as $field}
                    {if $user[$field] != ''}
                        <tr>
                            <td class="key">{$user->getProp($field)->getDescription()}</td>
                            <td class="value">{$user[$field]}</td>
                        </tr>
                    {/if}
                {/foreach}
            </tbody>
        </table>
    {else}
        <input type="hidden" name="user_type" value="{$order.user_type}">
        <div class="userProfile activeTabs" data-input-name="user_type">
            <div class="formSection">
                <div class="sectionListBlock">
                    <ul class="lineList tabList">
                        <li><a class="item {if $order.user_type=='person'} act{/if}" data-tab="#user-tab1" data-input-val="person" href="JavaScript:;">{t}Частное лицо{/t}</a></li>
                        <li><a class="item{if $order.user_type=='company'} act{/if}" data-tab="#user-tab1" data-class="thiscompany" data-input-val="company" href="JavaScript:;">{t}Компания{/t}</a></li>
                        <li><a class="item{if $order.user_type=='noregister'} act{/if}" data-tab="#user-tab2" data-input-val="noregister" href="JavaScript:;">{t}Без регистрации{/t}</a></li>
                    </ul>
                </div>
            </div>

            <div class="user-authorization-info">
                {t url=$user_config->getAuthorizationUrl()}Если Вы регистрировались ранее, пожалуйста, <a href="%url" class="inDialog">авторизуйтесь</a>.{/t}
            </div>
        
            <div class="tabFrame {if $order.user_type =='user' || $order.user_type =='noregister'} hidden{/if}{if $order.user_type =='company'} thiscompany{/if}" id="user-tab1">
                <table class="formTable">
                    <tbody class="organization">
                        <tr>
                            <td class="key">{t}Название организации{/t}:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_company')}
                                <div class="help">{t}Например: ООО Аудиторская фирма "Аудитор"{/t}</div>
                            </td>
                        </tr>
                        <tr>
                            <td class="key">{t}ИНН{/t}:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_company_inn')}
                                <div class="help">{t}10 или 12 цифр{/t}</div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody>
                    {if $user_config.user_one_fio_field}
                        <tr>
                            <td class="key">{t}Ф.И.О.{/t}:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_fio')}
                                <div class="help">{t}Например, Иванов Иван Иванович{/t}</div>
                            </td>
                        </tr>
                    {else}
                        {if $user_config->canShowField('name')}
                            <tr>
                                <td class="key">{t}Имя{/t}:</td>
                                <td class="value">
                                    {$order->getPropertyView('reg_name')}
                                    <div class="help">{t}Имя покупателя, владельца аккаунта{/t}</div>
                                </td>
                            </tr>
                        {/if}
                        {if $user_config->canShowField('surname')}
                            <tr>
                                <td class="key">{t}Фамилия{/t}:</td>
                                <td class="value">
                                    {$order->getPropertyView('reg_surname')}
                                    <div class="help">{t}Фамилия покупателя, владельца аккаунта{/t}</div>
                                </td>
                            </tr>
                        {/if}
                        {if $user_config->canShowField('midname')}
                            <tr>
                                <td class="key">{t}Отчество{/t}:</td>
                                <td class="value">
                                    {$order->getPropertyView('reg_midname')}
                                </td>
                            </tr>
                        {/if}
                    {/if}
                    {if $user_config->canShowField('phone')}
                        <tr>
                            <td class="key">{t}Телефон{/t}:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_phone')}
                                <div class="help">{t}В формате: +7(123)9876543{/t}</div>
                            </td>
                        </tr>
                    {/if}
                    {if $user_config->canShowField('login')}
                        <tr>
                            <td class="key">Логин:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_login')}
                            </td>
                        </tr>
                    {/if}
                    {if $user_config->canShowField('e_mail')}
                        <tr>
                            <td class="key">E-mail:</td>
                            <td class="value">
                                {$order->getPropertyView('reg_e_mail')}
                            </td>
                        </tr>
                    {/if}
                    <tr>
                        <td class="key">{t}Пароль{/t}:</td>
                        <td class="value">
                            <input type="checkbox" name="reg_autologin" {if $order.reg_autologin}checked{/if} value="1" id="reg-autologin">
                            <label for="reg-autologin">{t}Получить автоматически на e-mail или телефон{/t}</label>
                            <div class="help">{t}Нужен для проверки статуса заказа, обращения в поддержку, входа в кабинет{/t}</div>
                            <div id="manual-login" {if $order.reg_autologin}style="display:none"{/if}>
                                <div class="inline f">
                                    {$order.__reg_openpass->formView(['form'])}
                                    <div class="help">{t}Пароль{/t}</div>
                                </div>
                                <div class="inline">
                                    {$order.__reg_pass2->formView()}
                                    <div class="help">{t}Повтор пароля{/t}</div>
                                </div>
                                <div class="inline">
                                    <div class="form-error">{$order->getErrorsByForm('reg_openpass', ', ')}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                    {foreach from=$reg_userfields->getStructure() item=fld}
                        <tr>
                            <td class="key">{$fld.title}</td>
                            <td class="value">
                                {$reg_userfields->getForm($fld.alias)}
                                {assign var=errname value=$reg_userfields->getErrorForm($fld.alias)}
                                {assign var=error value=$order->getErrorsByForm($errname, ', ')}
                                {if !empty($error)}
                                    <span class="form-error">{$error}</span>
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    
                    </tbody>
                </table>
            </div>
            <div class="tabFrame{if $order.user_type !='noregister'} hidden{/if}" id="user-tab2">
                <table class="formTable">
                    <tbody>
                    <tr>
                        <td class="key">{t}Ф.И.О.{/t}:</td>
                        <td class="value">
                            {$order->getPropertyView('user_fio')}
                            <div class="help">{t}Фамилия, Имя и Отчество покупателя{/t}</div>
                        </td>
                    </tr>    
                    <tr>
                        <td class="key">E-mail:</td>
                        <td class="value">
                            {$order->getPropertyView('user_email')}
                            <div class="help">{t}E-mail покупателя{/t}</div>
                        </td>
                    </tr>  
                    <tr>
                        <td class="key">{t}Телефон{/t}:</td>
                        <td class="value">
                            {$order->getPropertyView('user_phone')}
                            <div class="help">{t}В формате{/t}: +7(123)9876543</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="tabFrame{if $order.user_type !='user'} hidden{/if}" id="user-tab3">
                <table class="formTable">
                    <tbody>
                    <tr>
                        <td class="key">{t}Логин{/t}:</td>
                        <td class="value">
                            {$order->getPropertyView('login')}
                        </td>
                    </tr>    
                    <tr>
                        <td class="key">{t}Пароль{/t}:</td>
                        <td class="value">
                            {$order->getPropertyView('password')}
                            <a href="?ologin=1" id="order_login">{t}Вход{/t}</a>
                        </td>
                    </tr>
                </table>
            </div>
       </div>
    {/if}
       
    {if $have_to_address_delivery && $shop_config->isCanShowAddress()}
        <div class="formSection">
            <span class="formSectionTitle">{t}адрес<{/t}/span>
        </div>
        {if $have_pickup_points} {* Если есть пункты самовывоза *}
            <div class="formPickUpTypeWrapper"> 
                <input id="onlyPickUpPoints" type="radio" name="only_pickup_points" value="1" {if $order.only_pickup_points}checked{/if}/> <label for="onlyPickUpPoints">{t}Самовывоз{/t}</label><br/>
                <input id="onlyDelivery" type="radio" name="only_pickup_points" value="0" {if !$order.only_pickup_points}checked{/if}/> <label for="onlyDelivery">{t}Доставка по адресу{/t}</label>
            </div>
        {/if}
        <div id="formAddressSectionWrapper" class="formAddressSectionWrapper {if $order.only_pickup_points && $shop_config->isCanShowAddress()}hidden{/if}">
            {if empty($address_list)}
                {$count_list = 0}
            {else}
                {$count_list = count($address_list)}
            {/if}
            {if $count_list>0}
                <div class="existsAddress">
                    {t}Использовать следующий адрес:{/t}
                    <table id="address-list">
                        {foreach from=$address_list item=address}
                        <tr>
                            <td><input type="radio" name="use_addr" value="{$address.id}" id="adr_{$address.id}" {if $order.use_addr == $address.id}checked{/if}></td>
                            <td>
                                <label for="adr_{$address.id}">{$address->getLineView()}</label>
                                <a href="{$router->getUrl('shop-front-checkout', ['Act' =>'deleteAddress', 'id' => $address.id])}" class="deleteAddress"></a>
                            </td>
                        </tr>
                        {/foreach}
                        <tr>
                            <td><input type="radio" name="use_addr" value="0" id="use_addr_new" {if $order.use_addr == 0}checked{/if}></td>
                            <td><label for="use_addr_new">{t}Другой адрес{/t}</label></td>
                        </tr>
                    </table>
                </div>
            {else}
                <input type="hidden" name="use_addr" value="0">
            {/if}
            <table class="formTable">
                <tbody class="new-address">
                    {if $shop_config.require_country}
                        <tr>
                            <td class="key">{t}Страна{/t}:</td>
                            <td class="value">
                                {assign var=region_tools_url value=$router->getUrl('shop-front-regiontools', ["Act" => 'listByParent'])}
                                {$order->getPropertyView('addr_country_id', ['data-region-url' => $region_tools_url])}
                                <div class="help">{t}Например: Россия{/t}</div>
                            </td>
                        </tr>
                    {/if}
                    {if $shop_config.require_region || $shop_config.require_city}
                        <tr>
                            <td class="key">{t}Область, Город{/t}:</td>
                            <td class="value">
                                {if $shop_config.require_region}
                                    <div class="inline f">
                                        {assign var=regcount value=$order->regionList()}
                                        <span {if count($regcount) == 0}style="display:none"{/if} id="region-select">
                                            {$order->getPropertyView('addr_region_id')}
                                        </span>

                                        <span {if count($regcount) > 0}style="display:none"{/if} id="region-input">
                                            {$order->getPropertyView('addr_region')}
                                        </span>
                                        <div class="help">{t}Область/Край{/t}</div>
                                    </div>
                                {/if}
                                {if $shop_config.require_city}
                                    <div class="inline">
                                        {$order->getPropertyView('addr_city')}
                                        <div class="help">{t}Город{/t}</div>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/if}
                    {if $shop_config.require_zipcode || $shop_config.require_address}
                        <tr>
                            <td class="key">{t}Индекс, Адрес{/t}:</td>
                            <td class="value">
                                {if $shop_config.require_zipcode}
                                    <div class="inline f">
                                        {$order->getPropertyView('addr_zipcode')}
                                        <div class="help">{t}Индекс{/t}</div>
                                    </div>
                                {/if}
                                {if $shop_config.require_address}
                                    <div class="inline">
                                        {$order->getPropertyView('addr_address')}
                                        <div class="help">{t}Адрес. Например: ул. Красная, 100, офис 71{/t}</div>
                                    </div>
                                {/if}
                            </td>
                        </tr>
                    {/if}
                    {if $shop_config.show_contact_person}
                        <tr>
                            <td class="key">{t}Контактное лицо{/t}:</td>
                            <td class="value">
                                {$order->getPropertyView('contact_person')}
                                <div class="help">{t}Лицо, которое встретит доставку. Например: Иван Иванович Пуговкин{/t}</div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
    {else}
        <input type="hidden" name="only_pickup_points" value="1"/>
    {/if}
    
    {if $order->__code->isEnabled()}
        <table class="formTable">
            <tbody>
                <tr>
                    <td class="key">{$order->__code->getTypeObject()->getFieldTitle()}</td>
                    <td class="value">{$order->getPropertyView('code')}</td>
                </tr>
            </tbody>
        </table>
    {/if}
    {if $conf_userfields->notEmpty()}
        <br>
        <div class="formSection">
            <span class="formSectionTitle">{t}дополнительные сведения{/t}</span>
        </div>
        <table class="formTable">
            <tbody>
            {foreach from=$conf_userfields->getStructure() item=fld}
            <tr>
                <td class="key">{$fld.title}</td>
                <td class="value">
                    {$conf_userfields->getForm($fld.alias)}
                    {assign var=errname value=$conf_userfields->getErrorForm($fld.alias)}
                    {assign var=error value=$order->getErrorsByForm($errname, ', ')}
                    {if !empty($error)}
                        <span class="form-error">{$error}</span>
                    {/if}
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    {/if}

    {if $CONFIG.enable_agreement_personal_data}
        {include file="%site%/policy/agreement_phrase.tpl" button_title="{t}Далее{/t}"}
    {/if}
         
    <button type="submit" class="formSave">{t}Далее{/t}</button>
</form>
<br><br><br>