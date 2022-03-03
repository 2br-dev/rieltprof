{extends file="help_wrapper.tpl"}
{block name="content"}
    {if count($languages)>1}
        <div style="float:right">
            {foreach $languages as $lang_item}
            &nbsp;&nbsp;<a href="?lang={$lang_item}" class="lang{if $lang_item == $lang} active{/if}">{$lang_item}</a>
            {/foreach}
        </div>
    {/if}
    <h1>{t}Описание методов API{/t}</h1>
    <p>{t alias="Для вызова метода достаточно выполнить GET или POST запрос.."
url=$router->getUrl('externalapi-front-apigate', [], true)
error_url=$router->getUrl('externalapi-front-apigate-help', ['method' => 'errors', 'lang' => $lang])
version=$current_version}Для вызова метода достаточно выполнить GET или POST запрос на адрес
    <pre><b>%url&lt;ИМЯ МЕТОДА&gt;?&lt;ПАРАМЕТРЫ&gt;</b>.</pre>
    Существуют частные параметры, присущие каждому методу индивидуально, а также общие параметры, которые принимаются всеми методами:
    <ul>
        <li><b>lang</b><i>(необязательный)</i> - двухсимвольный идентификатор языка, на котором следует вернуть результат запроса. В настоящее время поддерживается только русский (<b>ru</b>)</li>
        <li><b>v</b><i>(необязательный)</i> - версия. Каждый метод может быть представлен в нескольких версиях. И каждая версия метода может содержать разный набор параметров и возвращать разный результат. 
            Для стабильной работы вашего приложения, рекомендуем всегда указывать версию в запросах. В случае, если какого-либо метода не существует в указанной версии, будет вызвана первая доступная предыдущая версия метода.
            Список поддерживаемых версий указан напротив каждого метода. В случае, если данный параметр не будет указан, то будет использована версия <b>%version</b>.
        </li>
    </ul>
    
    <p>В случае успешного выполнения запроса, сервер вернет ответ в формате JSON, где корневая секция будет <b>response</b>.</p>
    <pre>
{
    "response": {
        ...
    }
}</pre>
    
    <p>В случае ошибки, сервер вернет ответ в формате JSON, где корневая секция будет <b>error</b>.</p>
    <pre>
{
    "error": {
        "code": <b><код ошибки></b>,
        "title": "<b><описание ошибки></b>"
    }
}</pre>

    <p>Полный список возможных ошибок представлен <a href="%error_url">здесь</a>.</p>
    <p>Ниже приводятся все методы для работы с данными текущего сайта. </p>{/t}
    {foreach $grouped_methods as $group_name => $methods}
    <h2>{$group_name}</h2>
    <table class="apihelp-table">
        <tbody>
            {foreach $methods as $method_name => $info}
            <tr>
                <td class="apihelp-table__method"><a href="{$router->getUrl('externalapi-front-apigate-help', ["method" => $method_name, "lang" => $lang])}">{$method_name}</a></td>
                <td class="apihelp-table__comment">{$info.comment}</td>
                <td class="apihelp-table__version">
                    {foreach $info.versions as $version}
                    v{$version}<br>
                    {/foreach}</td>        
            </tr>
            {/foreach}
        </tbody>
    </table>
    {/foreach}
{/block}