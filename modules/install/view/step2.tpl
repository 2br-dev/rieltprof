{extends file="%install%/wrap.tpl"}
{block name="content"}
<h2>{t}Проверка параметров сервера{/t}</h2>

<table class="check-options">
    <tr class="first">
        <td class="term">{t}PHP информация{/t}<br>
            <span class="expected">{t}Нажмите на ссылку, чтобы просмотреть все параметры PHP на вашем сервере{/t}</span>
        </td>
        <td width="150" class="conclusion no-check"><a href="{$router->getUrl('install', ['Act' => 'phpinfo'])}" target="_blank" class="phpinfo"><span class="hidden-mobile">{t}показать{/t}</span></a></td>
    </tr>
    <tr>
        <td class="term">{t ver=$check.php_version.server}Версия PHP (%ver){/t}<br>
            <span class="expected">{t}Требуется версия PHP не ниже{/t} {$check.php_version.need}</span>
        </td>
        <td class="conclusion{if !$check.php_version.decision} fail{/if}">
            <span>{if $check.php_version.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>
    <tr>
        <td class="term">MySQL<br>
            <span class="expected">{t}Версия MySQL должна быть {$check.mysql_support.need} или выше{/t}</span>
        </td>
        <td class="conclusion{if !$check.mysql_support.decision} fail{/if}">
            <span>{if $check.mysql_support.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>
    <tr>
        <td class="term">SafeMode<br>
            <span class="expected">{t}Защищенный режим должен быть отключен{/t}</span>
        </td>
        <td class="conclusion{if !$check.safe_mode.decision} fail{/if}">
            <span>{if $check.safe_mode.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>
    <tr>
        <td class="term">{t}Модуль GD{/t}<br>
            <span class="expected">{t}В системе должен быть установлен модуль GD для корректной работы с изображениями{/t}</span>
        </td>
        <td class="conclusion{if !$check.gd.decision} fail{/if}">
            <span>{if $check.gd.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>    
    <tr>
        <td class="term">{t}ZIP архивы{/t}<br>
            <span class="expected">{t}PHP должен поддерживать функции работы с zip архивами (Модуль ZIP){/t}</span>
        </td>
        <td class="conclusion{if !$check.zip.decision} fail{/if}">
            <span>{if $check.zip.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>        
    <tr>
        <td class="term">{t}Модуль MbString{/t}<br>
            <span class="expected">{t}Модуль mbstring должен быть включен с параметром mbstring.func_overload = 0 или 1, для работы UTF-8{/t}</span>
        </td>
        <td class="conclusion{if !$check.mbstring.decision} fail{/if}">
            <span>{if $check.mbstring.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>            
    <tr>
        <td class="term">{t}Модуль MCrypt или openSSL{/t}<br>
            <span class="expected">{t}Для корректной работы системы необходим модуль шифрования mcrypt с поддержкой алгоритма twofish или модуль openssl{/t}</span>
        </td>
        <td class="conclusion{if !$check.crypt.decision} fail{/if}">
            <span>{if $check.crypt.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>                
    <tr>
        <td class="term">{t}Загрузка файлов{/t}<br>
            <span class="expected">{t}Необходимо, чтобы загрузка файлов была включена{/t}</span>
        </td>
        <td class="conclusion{if !$check.upload_files.decision} fail{/if}">
            <span>{if $check.upload_files.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>
    <tr>
        <td class="term">{t}Поддержка CURL{/t}<br>
            <span class="expected">{t}Необходимо для выполнения некоторых операций, например соединение с платежными системами.{/t}</span>
        </td>
        <td class="conclusion{if !$check.curl.decision} fail{/if}">
            <span>{if $check.curl.decision}{t}соответствует{/t}{else}{t}не соответствует{/t}{/if}</span>
        </td>
    </tr>
</table>
<div class="hr"></div>
<h2>{t}Проверка прав доступа к файлам и папкам{/t}</h2>

<table class="check-options">
    {foreach from=$check.write_rights key=path item=data name="wr"}
    <tr {if $smarty.foreach.wr.first}class="first"{/if}>
        <td class="term">{$path} {if $path =='/'}({t}Корневая папка сайта{/t}){/if}<br>
            <span class="expected">{$data.description}</span>
        </td>
        <td width="150" class="conclusion{if !$data.decision} fail{/if}">
            <span>{if $data.decision}{t}запись возможна{/t}{else}{t}нет прав на запись{/t}{/if}</span>
        </td>
    </tr>
    {/foreach}
</table>

<div class="button-line mtop30">
    {if !$check.can_continue}
        <span class="page-error"><i></i>{t}Продолжение установки невозможно, необходимо чтобы все параметры соответствовали требуемым{/t}</span>
        <a href="{$router->getUrl('install', ['step' => '2'])}" class="next">{t}проверить еще раз{/t}</a>
    {else}
        <a href="{$router->getUrl('install', ['step' => '3'])}" class="next">{t}далее{/t}</a>
    {/if}
</div>
{/block}