<div style="padding: 40px;">
    <p style="margin-bottom: 8px;">{t}Данная тема оформления не поддерживает оформление заказа на одной странице.{/t}</p>
    <p>
        {t}Установите опцию "Тип оформления заказа" в значение "Оформление в 4 шага"{/t}
        {if $current_user->isAdmin()}
            <a href="{adminUrl mod_controller='modcontrol-control' do=edit mod=shop}">{t}в настройках модуля "Магазин"{/t}</a>
        {else}
            {t}в настройках модуля "Магазин"{/t}
        {/if}.
    </p>
</div>