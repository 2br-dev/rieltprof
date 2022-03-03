{* Шаблон, расширяющий список пунктов в меню личного кабинета *}
{$route_id=$router->getCurrentRoute()->getId()}
{modulegetvars name="\Support\Controller\Block\NewMessages" var="data"}
<li><a class="{if $route_id == 'support-front-support'}active{/if}" href="{$router->getUrl('support-front-support')}">{t}Сообщения{/t} <span class="supportCountMessages">({$data.new_count})</span></a></li>