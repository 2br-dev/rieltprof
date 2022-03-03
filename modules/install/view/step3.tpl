{extends file="%install%/wrap.tpl"}
{block name="content"}
<h2>{t}Конфигурирование системы{/t}</h2>
<h3>{t}Настройки базы данных MySQL{/t}</h3>
<form method="POST" id="config-form">
    <table class="config-table">
        <tr class="first">
            <td class="key">{t}Хост{/t}<br>
            <span class="key-help">{t}Например:{/t} 127.0.0.1</span>
            </td>
            <td class="value"><input type="text" name="db_host" value="{$Setup.INSTALL_DB_HOST}" data-check-pattern=".+" data-check-error="{t}Не заполнено обязательное поле Хост{/t}">
                <span class="field-error" data-field="db_host"></span>
            </td>
        </tr>
        <tr>
            <td class="key">{t}Порт{/t}<br>
                <span class="key-help">{t}Например:{/t} 3306</span>
            </td>
            <td class="value"><input type="text" name="db_port" value="{$Setup.INSTALL_DB_PORT}">
            </td>
        </tr>
        <tr>
            <td class="key">{t}Имя базы данных{/t}<br>
            <span class="key-help">{t}Например:{/t} readyscript</span>
            </td>
            <td class="value"><input type="text" name="db_name" value="{$Setup.INSTALL_DB_NAME}" data-check-pattern=".+" data-check-error="{t}Необходимо указать имя базы данных{/t}">
            <span class="field-error" data-field="db_name"></span>
            </td>
        </tr>    
        <tr>
            <td class="key">{t}Пользователь{/t}</td>
            <td class="value"><input type="text" name="db_user" value="{$Setup.INSTALL_DB_USERNAME}" data-check-pattern=".+" data-check-error="{t}Необходимо указать имя пользователя базы данных{/t}">
            <span class="field-error" data-field="db_user"></span></td>
        </tr>        
        <tr>
            <td class="key">{t}Пароль{/t}</td>
            <td class="value"><input type="text" name="db_pass" value="{$Setup.INSTALL_DB_PASSWORD}">
            <span class="field-error" data-field="db_pass">
            </td>
        </tr>            
        <tr>
            <td class="key">{t}Префикс к таблицам{/t}<br>
            <span class="key-help">{t}Любые буквы, цифры и знак подчеркивания{/t}</span>
            </td>
            <td class="value"><input type="text" name="db_prefix" value="{$generated_prefix}" data-check-pattern="^[_a-zA-Z0-9]*$" data-check-error="{t}Использованы недопустимые символы{/t}">
            <span class="field-error" data-field="db_prefix"></span>
            </td>
        </tr>            
    </table>
    <div class="hr sim"></div>
    <h3>{t}Администратор системы{/t}</h3>
    <p class="short-info">{t}Следующие данные будут использованы для входа в административную панель{/t}</p>
    <table class="config-table">
        <tr class="first">
            <td class="key">E-mail<br>
            <span class="key-help">{t}Используется в качестве логина{/t}</span>
            </td>
            <td class="value">
                <input type="text" name="supervisor_email" data-check-pattern="{literal}^[\.\-_A-Za-z0-9]+?@[\.\-A-Za-z0-9]+?\.[A-Za-z0-9]{2,6}${/literal}" data-check-error="{t}Необходимо указать корректный e-mail администратора{/t}" value="{$Setup.INSTALL_ADMIN_LOGIN}">
                <span class="field-error" data-field="supervisor_email">
            </td>
            <td>
                
            </td>
        </tr>
        <tr>
            <td class="key">{t}Пароль{/t}<br>
            <span class="key-help">{t}Должен быть не менее 6-ти символов{/t}</span>
            </td>
            <td class="value"><input type="password" name="supervisor_pass" data-check-pattern="{literal}^.{6,}${/literal}" data-check-error="{t}Пароль должен содержать не менее 6 знаков{/t}" value="{$Setup.INSTALL_ADMIN_PASSWORD}">
                <span class="field-error" data-field="supervisor_pass">
            </td>
            <td></td>
        </tr>    
        <tr>
            <td class="key">{t}Повтор пароля{/t}</td>
            <td class="value"><input type="password" name="supervisor_pass_confirm" data-check-error="{t}Неверный повтор пароля{/t}" value="{$Setup.INSTALL_ADMIN_PASSWORD}">
                <span class="field-error" data-field="supervisor_pass_confirm">
            </td>
        </tr>        
    </table>
    <div class="hr sim"></div>
    <h3>{t}Адрес административной части сайта{/t}</h3>
    <p class="short-info">{t}Изменение стандартного адреса административной части - рекомендуемая мера по увеличению безопасности сайта{/t}</p>
    <div class="change-admin-section">
        <span id="calc-width"></span><br>
        http://{$Setup.DOMAIN}/ <input type="text" name="admin_section" value="{$Setup.ADMIN_SECTION}" id="supervisor_pass_confirm" maxlength="20" data-check-pattern="^[a-z0-9\-_]+$" data-check-error="{t}В имени могут использоваться только цифры, английские буквы, символы '-_' тире и подчеркивания{/t}"> /
        <span class="field-error" data-field="admin_section"></span>
    </div>
    
<div class="hr sim"></div>
<div>
    <input type="checkbox" name="set_demo_data" value="1" id="demodata" {if $Setup.INSTALL_SET_DEMO_DATA}checked{/if}>&nbsp;&nbsp;<label for="demodata"><strong>{t}Устанавливать демонстрационные данные{/t}</strong></label>
</div>
</form>

<div class="button-line mtop30">
    <span class="page-error" style="display:none"><i></i>{t}Некоторые поля заполнены некорректно. Поля, в которых есть ошибки подсвечены красным цветом.{/t}</span>    
    <a data-href="{$router->getUrl('install', ['step' => '3'])}" data-next-url="{$router->getUrl('install', ['step' => '4'])}" class="next">{t}далее{/t}</a>
</div>
{/block}
{block name="root"}

<div class="progress-window">
    <div id="progress-run">
        <h2>{t}Установка системы{/t}</h2>
        <div class="progress">
            <div class="rails">
                <div class="border"></div>
                <div class="bar" style="width:0"><div class="percent"><span class="percent-value">0%</span><i></i></div></div>
            </div>
            <div class="status">
                {t}Подготовка к установке{/t}
            </div>
        </div>
    </div>

    <div id="progress-error">
        <h2>{t}Во время установки произошли ошибки{/t}</h2>
        <ul class="error-list">
            <li>
                <div class="field"><span class="module-title"></span><i class="cor"></i></div>
                <div class="text"></div>
            </li>
        </ul>
        <div class="buttons">
            <a class="button close-window">{t}Закрыть{/t}</a>
        </div>
    </div>
</div>

{/block}