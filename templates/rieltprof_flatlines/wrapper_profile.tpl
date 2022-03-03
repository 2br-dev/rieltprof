{*{extends file="%THEME%/wrapper.tpl"}*}
{block name="content"}
{$shop_config=ConfigLoader::byModule('shop')}
{$route_id=$router->getCurrentRoute()->getId()}
{*    <div class="box profile">*}
{*        *}{* Хлебные крошки *}
{*        {moduleinsert name="\Main\Controller\Block\BreadCrumbs"}*}
{*        {if $route_id != 'shop-front-myorderview'}<h1 class="textCenter">{t}Личный кабинет{/t}</h1>{/if}*}
{*        <div class="rel">*}
{*            <div class="rightColumn productList">*}
{*                *}
{*            </div>*}
{*            <div class="leftColumn">*}
{*                <ul class="profileMenu">*}
{*                    <li {if $route_id=='users-front-profile'}class="act"{/if}><a href="{$router->getUrl('users-front-profile')}">{t}Профиль{/t}</a></li>*}
{*                    <li {if in_array($route_id, ['shop-front-myorders', 'shop-front-myorderview'])}class="act"{/if}><a href="{$router->getUrl('shop-front-myorders')}">{t}Мои заказы{/t}</a></li>*}

{*                    {if $shop_config.return_enable}*}
{*                    <li {if $route_id == 'shop-front-myproductsreturn'}class="act"{/if}><a href="{$router->getUrl('shop-front-myproductsreturn')}">{t}Мои возвраты{/t}</a></li>*}
{*                    {/if}*}

{*                    {if $shop_config.use_personal_account}*}
{*                    <li {if $route_id=='shop-front-mybalance'}class="act"{/if}><a href="{$router->getUrl('shop-front-mybalance')}">{t}Лицевой счет{/t}</a></li>*}
{*                    {/if}*}
{*                    *}
{*                    {if ModuleManager::staticModuleExists('support')}*}
{*                    <li {if $route_id=='support-front-support'}class="act"{/if}><a href="{$router->getUrl('support-front-support')}">{t}Сообщения{/t}</a></li>*}
{*                    {/if}*}
{*                    *}
{*                    {if ModuleManager::staticModuleExists('partnership')}*}
{*                    {static_call var="is_partner" callback=['Partnership\Model\Api', 'isUserPartner'] params=$current_user.id}*}
{*                        {if $is_partner}*}
{*                            <li {if $route_id=='partnership-front-profile'}class="act"{/if}><a href="{$router->getUrl('partnership-front-profile')}">{t}Профиль партнера{/t}</a></li>*}
{*                        {/if}*}
{*                    {/if}*}
{*                    *}
{*                    <li><a href="{$router->getUrl('users-front-auth', ['Act' => 'logout'])}">{t}Выход{/t}</a></li>*}
{*                </ul>*}
{*            </div>*}
{*        </div>*}
{*        <div class="clearBoth"></div>*}
{*    </div>*}
    {$app->blocks->getMainContent()}
{/block}
