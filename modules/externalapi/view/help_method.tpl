{extends file="help_wrapper.tpl"}
    {block name="content"}
    <a href="{$router->getUrl('externalapi-front-apigate-help', [lang => $lang])}">&larr; {t}назад. к списку методов{/t}</a>
    <h1>{t method=$method}Описание метода <b>%method</b>{/t}</h1>
    
    <ul class="apihelp-tabs">
        {foreach $method_info as $version => $info}
        <li {if $info@first}class="active"{/if}><a href="#v{$version}" data-toggle="tab">{t}Версия{/t} {$version}</a></li>
        {/foreach}      
    </ul>
    
    <div class="apihelp-tab-content">
    {foreach $method_info as $version => $info}
        <div class="apihelp-tab-pane{if $info@first} active{/if}" id="v{$version}">
            <h2>{$info.method}</h2>
            <p>{$info.comment}</p>
            
            <h3>{t}Параметры{/t}</h3>
            <table class="apihelp-table">
                <tr>
                    <th>{t}Параметр{/t}</th>
                    <th>{t}Тип{/t}</th>
                    <th>{t}Наличие{/t}</th>
                    <th>{t}Значение по умолчанию{/t}</th>
                    <th>{t}Описание{/t}</th>
                </tr>            
            {foreach $info.params as $name => $param_info}
                {if !$param_info.is_disabled}
                <tr>
                    <td>{$param_info.name}</td>
                    <td>{$param_info.type}</td>
                    <td>{if $param_info.is_optional}{t}Опциональный{/t}{else}<b>{t}Обязательный{/t}</b>{/if}</td>
                    <td>{if is_string($param_info.default_value) || is_null($param_info.default_value)}{$param_info.default_value}{else}{var_export($param_info.default_value)}{/if}</td>
                    <td>{$param_info.comment}</td>
                </tr>
                {/if}
            {/foreach}
            </table>
            
            {if $info.return}
            <h3>{t}Результат{/t}</h3>
            <p>{$info.return}</p>
            {/if}
            
            {if $info.example}
            <h3>{t}Пример вызова{/t}</h3>
            <p>{$info.example}</p>
            {/if}
            
            <h3>{t}Класс-обработчик{/t}</h3>
            <pre>{$info.class}</pre>
        </div>
    {/foreach}
    </div>
{/block}