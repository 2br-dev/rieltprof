{$config = \RS\Config\Loader::ByModule('rieltprof')}
<div class="btn btn-popup" id="search-object">
    <span>Найти объект</span>
    <div class="popup">
        <a href="/catalog/kvartira/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-apartment.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Квартира</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Квартира')})</span>
            </div>
        </a>
        <a href="/catalog/dom/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-home.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Дом</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Дом')})</span>
            </div>
        </a>
        <a href="/catalog/komnata/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-rooms.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Комната</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Комната')})</span>
            </div>
        </a>
        <a href="/catalog/dacha/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-house.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Дача</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Дача')})</span>
            </div>
        </a>
        <a href="/catalog/uchastok/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-tree.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Участок</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Участок')})</span>
            </div>
        </a>
        <a href="/catalog/taunhaus/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Таунхаус</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Таунхаус')})</span>
            </div>
        </a>
        <a href="/catalog/dupleks/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Дуплекс</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Дуплекс')})</span>
            </div>
        </a>
        <a href="/catalog/novostroyka/">
            <div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-newbuilds.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Новостройка</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Новостройка')})</span>
            </div>
        </a>
        <a href="/catalog/garazh/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-garage.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Гараж</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Гараж')})</span>
            </div>
        </a>
        <a href="/catalog/kommerciya/">
            <div class="icon-holder">
                <img src="{$THEME_IMG}/categories-icons/black/icon-commerce.svg" alt="">
            </div>
            <div class="text-holder">
                <span>Коммерция</span>
                <span class="menu-count-object"> ({$config->getCountObjectByType('Коммерция')})</span>
            </div>
        </a>
    </div>
</div>
