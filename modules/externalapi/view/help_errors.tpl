{extends file="help_wrapper.tpl"}
{block name="content"}
    <a href="{$router->getUrl('externalapi-front-apigate-help')}">&larr; {t}назад. к списку методов{/t}</a>
    <h1>{t}Описание ошибок методов API{/t}</h1>
    <p>{t}Во время обращения к методам API могут возникнуть следующие ошибки. Сообщение об ошибке, в некоторых случаях, может быть более детальным непосредственно в теле JSON ответа.{/t}</p>
    {foreach $exceptions as $exception}
        {$info=$exception->getInfo($lang)}
        <h3>{$info.class_info}</h3>
        <table class="apihelp-table">
            <thead>
                <tr>
                    <th>{t}Код{/t}</th>
                    <th>{t}Описание{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach $info.error_codes as $data}
                <tr>
                    <td class="align-center">{$data.code}</td>
                    <td>{$data.message}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    {/foreach}
{/block}