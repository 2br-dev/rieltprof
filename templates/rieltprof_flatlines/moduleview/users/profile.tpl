{* Профиль пользователя *}
{addjs file="rs.profile.js"}
{$config = \RS\Config\Loader::byModule('rieltprof')}
{$categories = $user->getUniqCategoryAds()}
<div class="global-wrapper" id="account">
    <div class="sidebar" id="profile-sidebar">
        <div class="profile-block">
            <div class="img">
                <a href="/"><img src="{$THEME_IMG}/logo_dark_mode.svg" alt=""></a>
            </div>
            <div class="avatar lazy-image" data-src="{$user.__photo->getUrl('320', '320', 'xy')}"></div>
            {include file="%rieltprof%/rating_info.tpl" user=$user config=$config}
        </div>
        <form method="POST">
            {csrf}
            {$this_controller->myBlockIdInput()}
            <input type="hidden" name="referer" value="{$referer}">
            <input type="hidden" name="is_company" value="0"/>
            <div class="main-data">
                <div class="input-field no-available">
                    {$user->getPropertyView('surname', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Фамилия</label>
                    <div class="formFieldError">{$user->getErrorsByForm('surname', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('name', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">Имя</label>
                    <div class="formFieldError">{$user->getErrorsByForm('name', ',')}</div>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('midname', ['class' => 'nempty'])}
                    <label for="">Отчество</label>
                </div>
                <div class="input-field no-available">
                    {$user->getPropertyView('phone', ['class' => 'nempty phone_mask'], [form => true, errors => false])}
                    <label for="">Телефон</label>
                    <div class="formFieldError">{$user->getErrorsByForm('phone', ',')}</div>
                </div>
                <div class="input-field w100 no-available">
                    {$user->getPropertyView('e_mail', ['class' => 'nempty'], [form => true, errors => false])}
                    <label for="">E-mail</label>
                    <div class="formFieldError">{$user->getErrorsByForm('e_mail', ',')}</div>
                </div>
                <div class="w100">
                    <input type="checkbox" name="changepass" id="pass-accept" value="1" {if $user.changepass}checked{/if} class="checkbox">
                    <label for="pass-accept" class="check-label-w100">{t}Сменить пароль{/t}</label>
                </div>
                <div class="form-fields_change-pass{if !$user.changepass} hidden{/if}">
                    <div class="input-field w100">
                        {$user->getPropertyView('current_pass', ['class' => 'nempty'])}
                        <label for="">Старый пароль</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="openpass" {if count($user->getErrorsByForm('openpass'))}class="has-error"{/if}>
                        <label class="fieldName">{t}Пароль{/t}</label>
                        <div class="formFieldError">{$user->getErrorsByForm('openpass', ',')}</div>
                    </div>
                    <div class="input-field">
                        <input type="password" name="openpass_confirm">
                        <label class="fieldName">{t}Повтор пароля{/t}</label>
                        <div class="formFieldError">{$user->getErrorsByForm('openpass_confirm', ',')}</div>
                    </div>
                    <div class="actions">
                        {*                <a href="" class="btn critical">Удалить профиль</a>*}
                        <button type="submit" class="link link-more btn">{t}Сохранить{/t}</button>
                    </div>
                </div>
                <div class="action">
                    <a href="{$router->getUrl('rieltprof-front-auth', ['Act' => 'logout'])}" class="btn critical">Выход</a>
                </div>
            </div>
            {if $conf_userfields->notEmpty()}
                {foreach $conf_userfields->getStructure() as $fld}
                    <div class="form-group">
                        <label class="label-sup">{$fld.title}</label>
                        {$conf_userfields->getForm($fld.alias)}

                        {$errname = $conf_userfields->getErrorForm($fld.alias)}
                        {$error = $user->getErrorsByForm($errname, ', ')}
                        {if !empty($error)}
                            <span class="formFieldError">{$error}</span>
                        {/if}
                    </div>
                {/foreach}
            {/if}
            <div class="separator"></div>
        </form>
    </div>
    <div class="content">
        <div class="top-block">
            <div class="row">
                <div class="add-object-wrapper">
                    {include file='%rieltprof%/add-object-menu.tpl' referer='/my/'}
                    {include file='%rieltprof%/search-object-menu.tpl'}
                </div>
                <div class="separator"></div>
                <div class="switch-district">
                    <div class="segmented-radio">
                        {if $smarty.server.REQUEST_URI == "/my/"}
                            <p class="sale-category-link active">Мои объекты</p>
                        {else}
                            <a href="/my/" class="sale-category-link">Мои объекты</a>
                        {/if}
                        {if $smarty.server.REQUEST_URI == "/my-review/"}
                            <p class="rent-category-link active">Мои отзывы</p>
                        {else}
                            <a href="/my-review/" class="rent-category-link">Мои отзывы</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="main-block">
            {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}
            <div class="title">
                <span>Мои объекты
                    {if $user->getCountAds($user['id'])}
                        ({$user->getCountAds($user['id'])})
                    {/if}
                </span>
            </div>
            <div class="tabs-wrapper">
                <div class="tabs-header">
                    <a href="" class="tab-link active" data-target="sell">Продажа</a>
                    <a href="" class="tab-link" data-target="rent">Аренда</a>
                </div>
                <div class="tabs">
                    <div class="tab active" id="sell">
                            {foreach $categories as $category}
                            {if $category['parent'] == 'Продажа'}
                                <div class="group-header">{$category['name']}</div>
                                {$ads = $user->getAdsFromCategoryById($category['id'])}
                                {if $ads}
                                    <table class="table-view" data-mode="list">
                                        <thead>
                                            <tr>
{*                                                <th>&nbsp;</th>*}
                                                <th>&nbsp;</th>
                                                <th class="features">&nbsp;</th>
                                                <th class="district">Район</th>
                                                <th class="price">Стоимость</th>
                                                <th class="rooms">Комнат</th>
                                                <th class="date">Дата</th>
                                                <th class="personal-note">Личные заметки</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody class="my-objects-data">
                                            {foreach $ads as $ad}
                                                <tr data-id="{$ad['id']}" class="object-link {if !$ad['public']}not-actual{/if}" id="ad-{$ad['id']}">
{*                                                    <td class="expand-wrapper"><a href="javascript:void(0);" class="expand-row"></a></td>*}
                                                    <td class="photo-holder"><a href="" class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('360', '215', 'xy')}"></a></td>
                                                    <td class="features">
                                                        <div class="features">
                                                            {include file="%catalog%/fitures-table.tpl" product=$ad}
                                                        </div>
                                                    </td>
                                                    <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>
                                                    <td class="price">{if $ad['cost_product']}
                                                            {$config->formatCost($ad['cost_product'], ' ')} ₽
                                                        {else}
                                                            {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.
                                                        {/if}</td>
                                                    <td class="rooms">
                                                        {if $ad['rooms']}
                                                            {$ad['rooms']}
                                                        {else}
                                                            {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
                                                                {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                                                                    Студия
                                                                {else}
                                                                    {$ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}
                                                                {/if}
                                                            {else}
                                                                -
                                                            {/if}
                                                        {/if}
                                                    </td>
                                                    <td class="date">
                                                        {$ad->dateFormat('d.m.Y', 'dateof')}
                                                    </td>
                                                    <td class="personal-note">{$ad['personal_note']}</td>
                                                    <td class="actions">
                                                        {$cont = 'rieltprof-'|cat:$ad['controller']}
{*                                                        {if !$config->isActualAd($ad)}*}
{*                                                            <div class="info" title="Не актуализировался более 30 дней">&nbsp;</div>*}
{*                                                        {/if}*}
                                                        {if !$ad['public']}
                                                            <a
                                                                data-url="{$router->getUrl('rieltprof-front-objects', ['Act' => 'republish'])}"
                                                                data-id="{$ad['id']}"
                                                                data-type="{$ad['controller']}"
                                                                class="republish-object"
                                                                title="Опубликовать"
                                                            ></a>
                                                        {/if}
                                                        <a href="{$router->getAdminUrl('edit', ['referer' => '/my/', 'id' => $ad['id'], 'dir' => $category['id'], 'action' => 'sale', 'object' => $ad['object']], $cont)}" class="edit crud-edit">&nbsp;</a>
                                                        <a href="{$router->getAdminUrl('delProd', ['referer' => '/my/', 'id' => $ad['id']], $cont)}" class="remove delete crud-get crud-close-dialog">&nbsp;</a>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        </tbody>
                                    </table>
                                {/if}
                            {/if}
                        {/foreach}
                    </div>
                    <div class="tab" id="rent">
                            {foreach $categories as $category}
                                {if $category['parent'] == 'Аренда'}
                                <div class="group-header">{$category['name']}</div>
                                {$ads = $user->getAdsFromCategoryById($category['id'])}
                                {if $ads}
                                    <table class="table-view" data-mode="list">
                                        <thead>
                                        <tr>
{*                                            <th>&nbsp;</th>*}
                                            <th>&nbsp;</th>
                                            <th class="features">&nbsp;</th>
                                            <th class="district">Район</th>
                                            <th class="price">Стоимость</th>
                                            <th class="rooms">Комнат</th>
                                            <th class="date">Дата</th>
                                            <th class="personal-note">Личные заметки</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody class="my-objects-data">
                                        {foreach $ads as $ad}
                                            <tr data-id="{$ad['id']}" class="object-link">
{*                                                <td class="expand-wrapper"><a href="javascript:void(0);" class="expand-row"></a></td>*}
                                                <td class="photo-holder"><a href="" class="photo lazy-image" data-src="{$ad->getMainImage()->getUrl('160', '180', 'axy')}"></a></td>
                                                <td class="features">
                                                    <div class="features">
                                                        {include file="%catalog%/fitures-table.tpl" product=$ad}
                                                    </div>
                                                </td>
                                                <td class="district">{$ad->getProductPropValue($config['prop_district'], 'district')}</td>
                                                <td class="price">{if $ad['cost_product']}
                                                        {$config->formatCost($ad['cost_product'], ' ')} ₽
                                                    {else}
                                                        {$config->formatCost($ad['cost_rent'], ' ')} ₽/мес.
                                                    {/if}</td>
                                                <td class="rooms">
                                                    {if $ad['rooms']}
                                                        {$ad['rooms']}
                                                    {else}
                                                        {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') !== NULL}
                                                            {if $ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list') == 'Студия'}
                                                                Студия
                                                            {else}
                                                                {$ad->getProductPropValue($config['prop_rooms_list'], 'rooms_list')}
                                                            {/if}
                                                        {else}
                                                            -
                                                        {/if}
                                                    {/if}
                                                </td>
                                                <td class="date">
                                                    {$ad->dateFormat('d.m.Y', 'dateof')}
                                                </td>
                                                <td class="personal-note">{$ad['personal_note']}</td>
                                                <td class="actions">
                                                    {$cont = 'rieltprof-'|cat:$ad['controller']}
                                                    {if !$config->isActualAd($ad)}
                                                        <div class="info" title="Не актуализировался более 30 дней">&nbsp;</div>
                                                    {/if}
                                                    <a href="{$router->getAdminUrl('edit', ['referer' => '/my/', 'id' => $ad['id'], 'dir' => $category['id'], 'action' => 'rent', 'object' => $ad['object']], $cont)}" class="edit crud-edit">&nbsp;</a>
                                                    <a href="{$router->getAdminUrl('delProd', ['referer' => '/my/', 'id' => $ad['id']], $cont)}" class="remove delete crud-get crud-close-dialog">&nbsp;</a>
                                                </td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                {/if}
                            {/if}
                            {/foreach}

                    </div>
                </div>
            </div>

        </div>
    </div>
    {include file='%rieltprof%/statusbar.tpl'}
</div>





{*<div class="form-style">*}
{*    <div class="tab-content">*}
{*        <div id="menu1" class="tab-pane fade active in">*}
{*            <div class="col-xs-12">*}
{*                <h3 class="h3">{t}Личные данные{/t}</h3>*}

{*                {if $errors=$user->getNonFormErrors()}*}
{*                    <div class="page-error">*}
{*                        {foreach $errors as $item}*}
{*                            <div class="item">{$item}</div>*}
{*                        {/foreach}*}
{*                    </div>*}
{*                {/if}*}

{*                {if $result}*}
{*                    <div class="page-success-result">{$result}</div>*}
{*                {/if}*}

{*                <form method="POST">*}
{*                    {csrf}*}
{*                    {$this_controller->myBlockIdInput()}*}
{*                    <input type="hidden" name="referer" value="{$referer}">*}

{*                    <div class="form-group">*}
{*                        <input type="radio" name="is_company" value="0" id="is_company_no" {if !$user.is_company}checked{/if}><label for="is_company_no">{t}Частное лицо{/t}</label><br>*}
{*                        <input type="radio" name="is_company" value="1" id="is_company_yes" {if $user.is_company}checked{/if}><label for="is_company_yes">{t}Юридическое лицо или ИП{/t}</label>*}
{*                    </div>*}

{*                    <div class="form-fields_company{if !$user.is_company} hidden{/if}">*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Наименование компании{/t}</label>*}
{*                            {$user->getPropertyView('company', ['placeholder' => "{t}Например, ООО Ромашка{/t}"])}*}
{*                        </div>*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}ИНН{/t}</label>*}
{*                            {$user->getPropertyView('company_inn', ['placeholder' => "{t}10 или 12 цифр{/t}"])}*}
{*                        </div>*}
{*                    </div>*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Имя{/t}</label>*}
{*                        {$user->getPropertyView('name', ['placeholder' => "{t}Например, Иван{/t}"])}*}
{*                    </div>*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Фамилия{/t}</label>*}
{*                        {$user->getPropertyView('surname', ['placeholder' => "{t}Например, Иванов{/t}"])}*}
{*                    </div>*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Отчество{/t}</label>*}
{*                        {$user->getPropertyView('midname', ['placeholder' => "{t}Например, Иванович{/t}"])}*}
{*                    </div>*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}Телефон{/t}</label>*}
{*                        {$user->getPropertyView('phone', ['placeholder' => "{t}Например, +7(XXX)-XXX-XX-XX{/t}"])}*}
{*                    </div>*}

{*                    <div class="form-group">*}
{*                        <label class="label-sup">{t}E-mail{/t}</label>*}
{*                        {$user->getPropertyView('e_mail', ['placeholder' => "{t}Например, demo@example.com{/t}"])}*}
{*                    </div>*}

{*                    {if $conf_userfields->notEmpty()}*}
{*                        {foreach $conf_userfields->getStructure() as $fld}*}
{*                            <div class="form-group">*}
{*                                <label class="label-sup">{$fld.title}</label>*}
{*                                {$conf_userfields->getForm($fld.alias)}*}

{*                                {$errname = $conf_userfields->getErrorForm($fld.alias)}*}
{*                                {$error = $user->getErrorsByForm($errname, ', ')}*}
{*                                {if !empty($error)}*}
{*                                    <span class="formFieldError">{$error}</span>*}
{*                                {/if}*}
{*                            </div>*}

{*                        {/foreach}*}
{*                    {/if}*}

{*                    <div class="form_label__block form-group">*}
{*                        <input type="checkbox" name="changepass" id="pass-accept" value="1" {if $user.changepass}checked{/if}>*}
{*                        <label for="pass-accept">{t}Сменить пароль{/t}</label>*}
{*                    </div>*}

{*                    <div class="form-fields_change-pass{if !$user.changepass} hidden{/if}">*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Старый пароль{/t}</label>*}
{*                            {$user->getPropertyView('current_pass')}*}
{*                        </div>*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Пароль{/t}</label>*}
{*                            {$user->getPropertyView('openpass')}*}
{*                        </div>*}
{*                        <div class="form-group">*}
{*                            <label class="label-sup">{t}Повтор пароля{/t}</label>*}
{*                            {$user->getPropertyView('openpass_confirm')}*}
{*                        </div>*}
{*                    </div>*}

{*                    <div class="form__menu_buttons">*}
{*                        <button type="submit" class="link link-more">{t}Сохранить{/t}</button>*}
{*                    </div>*}
{*                </form>*}
{*            </div>*}
{*        </div>*}
{*    </div>*}
{*</div>*}
