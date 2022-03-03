{if $smarty.const.SCRIPT_TRIAL_STATUS != 'DISABLED' 
    || $smarty.const.SCRIPT_TRIAL_STATUS == 'OVERDUE' 
    || $smarty.const.SITE_LIMIT_ERROR 
    || $smarty.const.SCRIPT_TYPE_ERROR}

    {if $smarty.const.SCRIPT_TRIAL_STATUS != 'DISABLED'}
        <div class="notice-box" style="margin-top:10px;">
                {t alias="Весь функционал доступен в полном объеме.."}ReadyScript работает в <strong>пробном режиме</strong>. Весь функционал доступен в полном объеме.
                После окончания пробного периода сайты прекратят свою работу.
                Исключение составляют сайты, расположенные на доменных именах для разработки - .local и .test,
                их работа будет продолжена после окончания пробного периода.{/t}
        </div>
    {/if}

    {if $smarty.const.SCRIPT_TRIAL_STATUS == 'OVERDUE'
        || $smarty.const.SITE_LIMIT_ERROR
        || $smarty.const.SCRIPT_TYPE_ERROR}
        <div class="notice-box">
            {if $smarty.const.SCRIPT_TRIAL_STATUS == 'OVERDUE'}<p>{t}Пробный период истек, необходимо добавить лицензию.{/t}</p>{/if}
            {if $smarty.const.SITE_LIMIT_ERROR}<p>{t}Превышено число сайтов, разрешенных в лицензии{/t}</p>{/if}
            {if $smarty.const.SCRIPT_TYPE_ERROR}<p>{t}Комплектация продукта не соответствует лицензии{/t}</p>{/if}
        </div>
    {/if}
{/if}

<div class="notice-box" style="margin-top:10px">
    {t}Текущая комплектация системы{/t}: <strong>{$Setup.SCRIPT_TYPE}</strong>. {t}Версия ядра{/t}: <strong>{$Setup.VERSION}</strong>
</div>