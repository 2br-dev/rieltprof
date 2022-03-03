<div class="avatar-holder">
    {$user = \RS\Application\Auth::getCurrentUser()}
{*    <a href="/my/">*}
        <div class="avatar lazy-image" data-src="{$user.__photo->getUrl('160', '160', 'xy')}"></div>
{*    </a>*}
    <ul class="user-popup">
        <li><a href="/my/">Профиль</a></li>
        <li><a href="{$router->getUrl('rieltprof-front-auth', ['Act' => 'logout'])}">Выход</a></li>
    </ul>
</div>

{if isset($smarty.cookies.action_folder) && $smarty.cookies.action_folder == 'rent'}
    <div class="navigation-holder" id="navigation">
        <a title="Квартира-Аренда" href="/catalog/kvartira-rent/" {if $smarty.server.REQUEST_URI == "/catalog/kvartira-rent/"}class="active"{/if} title="Квартира"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-apartment.svg" alt=""></span><span class="text-holder">Квартира</span></a>
        <a title="Дом-Аренда" href="/catalog/dom-rent/" {if $smarty.server.REQUEST_URI == "/catalog/dom-rent/"}class="active"{/if} title="Дом"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-home.svg" alt=""></span><span class="text-holder">Дом</span></a>
        <a title="Комната-Аренда" href="/catalog/komnata-rent/" {if $smarty.server.REQUEST_URI == "/catalog/komnata-rent/"}class="active"{/if} title="Комната"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-rooms.svg" alt=""></span><span class="text-holder">Комната</span></a>
        <a title="Дача-Аренда" href="/catalog/dacha-rent/" {if $smarty.server.REQUEST_URI == "/catalog/dacha-rent/"}class="active"{/if} title="Дача"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-house.svg" alt=""></span><span class="text-holder">Дача</span></a>
        <a title="Участок-Аренда" href="/catalog/uchastok-rent/" {if $smarty.server.REQUEST_URI == "/catalog/uchastok-rent/"}class="active"{/if} title="Участок"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tree.svg" alt=""></span><span class="text-holder">Участок</span></a>
        <a title="Таунхаус-Аренда" href="/catalog/taunhaus-rent/" {if $smarty.server.REQUEST_URI == "/catalog/taunhaus-rent/"}class="active"{/if} title="Таунхаус"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tounhouses.svg" alt=""></span><span class="text-holder">Таунхаус</span></a>
        <a title="Дуплекс-Аренда" href="/catalog/dupleks-rent/" {if $smarty.server.REQUEST_URI == "/catalog/dupleks-rent/"}class="active"{/if} title="Дуплекс"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tounhouses.svg" alt=""></span><span class="text-holder">Дуплекс</span></a>
        <a title="Гараж-Аренда" href="/catalog/garazh-rent/" {if $smarty.server.REQUEST_URI == "/catalog/garazh-rent/"}class="active"{/if} title="Гараж"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-garage.svg" alt=""></span><span class="text-holder">Гараж</span></a>
        <a title="Коммерция-Аренда" href="/catalog/kommerciya-rent/" {if $smarty.server.REQUEST_URI == "/catalog/kommerciya-rent/"}class="active"{/if} title="Комерция"><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-commerce.svg" alt=""></span><span class="text-holder">Коммерция</span></a>
    </div>
{else}
    <div class="navigation-holder" id="navigation">
        <a title="Квартира-Продажа" href="/catalog/kvartira/" {if $smarty.server.REQUEST_URI == "/catalog/kvartira/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-apartment.svg" alt=""></span><span class="text-holder">Квартира</span></a>
        <a title="Дом-Продажа" href="/catalog/dom/" {if $smarty.server.REQUEST_URI == "/catalog/dom/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-home.svg" alt=""></span><span class="text-holder">Дом</span></a>
        <a title="Комната-Продажа" href="/catalog/komnata/" {if $smarty.server.REQUEST_URI == "/catalog/komnata/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-rooms.svg" alt=""></span><span class="text-holder">Комната</span></a>
        <a title="Дача-Продажа" href="/catalog/dacha/" {if $smarty.server.REQUEST_URI == "/catalog/dacha/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-house.svg" alt=""></span><span class="text-holder">Дача</span></a>
        <a title="Участок-Продажа" href="/catalog/uchastok/" {if $smarty.server.REQUEST_URI == "/catalog/uchastok/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tree.svg" alt=""></span><span class="text-holder">Участок</span></a>
        <a title="Таунхаус-Продажа" href="/catalog/taunhaus/" {if $smarty.server.REQUEST_URI == "/catalog/taunhaus/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tounhouses.svg" alt=""></span><span class="text-holder">Таунхаус</span></a>
        <a title="Дуплекс-Продажа" href="/catalog/dupleks/" {if $smarty.server.REQUEST_URI == "/catalog/dupleks/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-tounhouses.svg" alt=""></span><span class="text-holder">Дуплекс</span></a>
        <a title="Новостройка-Продажа" href="/catalog/novostroyka/" {if $smarty.server.REQUEST_URI == "/catalog/novostroyka/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-newbuilds.svg" alt=""></span><span class="text-holder">Новостройка</span></a>
        <a title="Гараж-Продажа" href="/catalog/garazh/" {if $smarty.server.REQUEST_URI == "/catalog/garazh/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-garage.svg" alt=""></span><span class="text-holder">Гараж</span></a>
        <a title="Коммерция-Продажа" href="/catalog/kommerciya/" {if $smarty.server.REQUEST_URI == "/catalog/kommerciya/"}class="active"{/if}><span class="img-holder"><img src="{$THEME_IMG}/categories-icons/blue/icon-commerce.svg" alt=""></span><span class="text-holder">Коммерция</span></a>
    </div>
{/if}
<div class="expand-holder">
    <a href="" class="expand-btn"></a>
</div>
<div class="burger-wrapper">
    <div class="a burger" data-target="navigation">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
</div>
