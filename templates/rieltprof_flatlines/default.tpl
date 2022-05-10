{$current_user = \RS\Application\Auth::getCurrentUser()}
{*{if $is_auth}*}
{*    {if $current_user['access']}*}
        {block name="content"}
            {$app->blocks->getMainContent()}
        {/block}
{*    {else}*}
{*        <p>Доступ на сайт для вашего эккаунта заблокирован. Обратитесь а администратору сайта.</p>*}
{*    {/if}*}
{*{else}*}
{*    {moduleinsert name="\Rieltprof\Controller\Block\Login" indexTemplate="%users%/authorization.tpl"}*}
{*{/if}*}
