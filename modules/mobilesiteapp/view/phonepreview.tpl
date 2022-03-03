{addcss file="%mobilesiteapp%/phonepreview.css"}

<div class="phoneInsideWrapper">
    <div class="phoneTopHeader" style="background-color: {$info.settings.color_primary}">
        <a class="phoneMenu" style="color: {$info.settings.color_white}"></a>
        <span class="phoneHeaderTitle" style="color: {$info.settings.color_white}">{t}Название сайта{/t}</span>
    </div>
    <div class="phoneContent">
        <ul class="phoneTopCategory">
            <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Демо-продукты{/t}</span></li>
            <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Электроника{/t}</span></li>
            <ul class="phoneTopSubCategory" style="background-color: {$info.settings.color_graylight}">
                <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Планшеты{/t}</span></li>
                <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Телефоны{/t}</span></li>
                <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Смартфоны{/t}</span></li>
            </ul>
            <li style="border-color: {$info.settings.color_gray}"><span style="color: {$info.settings.color_dark}">{t}Цветы{/t}</span></li>
        </ul>
        <div class="phoneBlueHeader" style="background-color: {$info.settings.color_primary}">
            <span style="color: {$info.settings.color_white}">{t}Лидеры продаж{/t}</span>
        </div>
        <ul class="phoneCategory">
            <li class="phoneProduct" style="border-color: {$info.settings.color_gray}">
                <span class="heart full" style="color: {$info.settings.color_danger}"></span>
                <img src="{$mod_img}usermobile/product1.png" alt=""/>
                <div class="title" style="color: {$info.settings.color_dark}">
                    HTC A6380 Gratia Green
                </div>
                <div class="price" style="color: {$info.settings.color_dark}">
                    10 500 р.
                </div>
            </li>
            <li class="phoneProduct" style="border-color: {$info.settings.color_gray}">
                <span class="heart" style="color: {$info.settings.color_graymilk}"></span>
                <img src="{$mod_img}usermobile/product2.png" alt=""/>
                <div class="title" style="color: {$info.settings.color_dark}">
                    {t}Блуза Incity{/t}
                </div>
                <div class="price" style="color: {$info.settings.color_dark}">
                    2 499 р.
                </div>
            </li>
            <li class="phoneProduct" style="border-color: {$info.settings.color_gray}">
                <span class="heart" style="color: {$info.settings.color_graymilk}"></span>
                <img src="{$mod_img}usermobile/product3.png" alt=""/>
                <div class="title" style="color: {$info.settings.color_dark}">
                    {t}Лабутены{/t}
                </div>
                <div class="price" style="color: {$info.settings.color_dark}">
                    15 999 р.
                </div>
            </li>
            <li class="phoneProduct" style="border-color: {$info.settings.color_gray}">
                <span class="heart" style="color: {$info.settings.color_graymilk}"></span>
                <img src="{$mod_img}usermobile/product4.png" alt=""/>
                <div class="title" style="color: {$info.settings.color_dark}">
                    {t}Босоножки{/t}
                </div>
                <div class="price" style="color: {$info.settings.color_dark}">
                    1 200 р.
                </div>
            </li>
        </ul>

        <div class="phoneButton blue" style="background-color: {$info.settings.color_primary}">{t}Поиск{/t}</div>
        <div class="phoneButton blue phoneEmpty" style="border-color: {$info.settings.color_primary}"><span style="color: {$info.settings.dark}">{t}Поиск{/t}</span></div>
        <div class="phoneButton orange" style="background-color: {$info.settings.color_orange}">{t}Заказать{/t}</div>
        <div class="phoneButton orange phoneEmpty" style="border-color: {$info.settings.color_orange}"><span style="color: {$info.settings.dark}">{t}Купить в один клик{/t}</span></div>
    </div>
    <ul class="phoneBottom" style="background-color: {$info.settings.color_light}">
        <li class="act" style="color: {$info.settings.color_primary}">
            <div class="icon home"></div>
            <div class="title">{t}Каталог{/t}</div>
        </li>
        <li style="color: {$info.settings.color_graydark}">
            <div class="icon search"></div>
            <div class="title">{t}Поиск{/t}</div>
        </li>
        <li style="color: {$info.settings.color_graydark}">
            <div class="icon favorite"></div>
            <span class="iconhint" style="background-color: {$info.settings.color_danger}"><span style="color: {$info.settings.color_white}">3</span></span>
            <div class="title">{t}Избранное{/t}</div>
        </li>
        <li style="color: {$info.settings.color_graydark}">
            <div class="icon cart"></div>
            <div class="title">{t}Корзина{/t}</div>
        </li>
    </ul>
</div>