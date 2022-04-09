{addjs file="%deliverycost%/deliverycost.js"}
{addcss file="%deliverycost%/deliverycost.css"}   
{$city.title} <a data-href="{$router->getUrl('deliverycost-front-choosecityautocomplete', ['redirect' => urlencode($smarty.server.REQUEST_URI)])}" class="inDialog">{t}Выбрать город{/t}</a>