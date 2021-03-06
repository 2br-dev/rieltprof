<div class="btn btn-popup" id="add-object">
    <span>Добавить объект</span>
    <div class="popup">
        <ul>
            <li>
                <span>Продажа</span>
                <ul>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 3, 'rent_dir' => 13, 'object' => 'Квартира', 'action' => 'sale'], 'rieltprof-flatctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-apartment.svg" alt=""></div><div class="text-holder"><span>Квартира</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 4, 'rent_dir' => 14, 'object' => 'Новостройка', 'action' => 'sale'], 'rieltprof-newbuildingctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-newbuilds.svg" alt=""></div><div class="text-holder"><span>Новостройка</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 6, 'rent_dir' => 16, 'object' => 'Дом', 'action' => 'sale'], 'rieltprof-housectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-home.svg" alt=""></div><div class="text-holder"><span>Дом</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 5, 'rent_dir' => 15, 'object' => 'Комната', 'action' => 'sale'], 'rieltprof-roomctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-rooms.svg" alt=""></div><div class="text-holder"><span>Комната</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 10, 'rent_dir' => 20, 'object' => 'Дача', 'action' => 'sale'], 'rieltprof-countryhousectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-house.svg" alt=""></div><div class="text-holder"><span>Дача</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 9, 'rent_dir' => 19, 'object' => 'Участок', 'action' => 'sale'], 'rieltprof-plotctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tree.svg" alt=""></div><div class="text-holder"><span>Участок</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 7, 'rent_dir' => 17, 'object' => 'Таунхаус', 'action' => 'sale'], 'rieltprof-townhousectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt=""></div><div class="text-holder"><span>Таунхаус</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 8, 'rent_dir' => 18, 'object' => 'Дуплекс', 'action' => 'sale'], 'rieltprof-duplexctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt=""></div><div class="text-holder"><span>Дуплекс</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 11, 'rent_dir' => 21, 'object' => 'Гараж', 'action' => 'sale'], 'rieltprof-garagectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-garage.svg" alt=""></div><div class="text-holder"><span>Гараж</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 12, 'rent_dir' => 22, 'object' => 'Коммерция', 'action' => 'sale'], 'rieltprof-commercialctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-commerce.svg" alt=""></div><div class="text-holder"><span>Коммерция</span></div></a></li>
                </ul>
            </li>
            <li>
                <span>Аренда</span>
                <ul>
                    <li>
                        <a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => '/', 'sale_dir' => 3, 'rent_dir' => 13, 'object' => 'Квартира', 'action' => 'rent'], 'rieltprof-flatctrl')}">
                            <div class="icon-holder">
                                <img src="{$THEME_IMG}/categories-icons/black/icon-apartment.svg" alt="">
                            </div>
                            <div class="text-holder">
                                <span>Квартира</span>
                            </div>
                        </a>
                    </li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 6, 'rent_dir' => 16, 'object' => 'Дом', 'action' => 'rent'], 'rieltprof-housectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-home.svg" alt=""></div><div class="text-holder"><span>Дом</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 5, 'rent_dir' => 15, 'object' => 'Комната', 'action' => 'rent'], 'rieltprof-roomctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-rooms.svg" alt=""></div><div class="text-holder"><span>Комната</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 10, 'rent_dir' => 20, 'object' => 'Дача', 'action' => 'rent'], 'rieltprof-countryhousectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-house.svg" alt=""></div><div class="text-holder"><span>Дача</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 9, 'rent_dir' => 19, 'object' => 'Участок', 'action' => 'rent'], 'rieltprof-plotctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tree.svg" alt=""></div><div class="text-holder"><span>Участок</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 7, 'rent_dir' => 17, 'object' => 'Таунхаус', 'action' => 'rent'], 'rieltprof-townhousectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt=""></div><div class="text-holder"><span>Таунхаус</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 8, 'rent_dir' => 18, 'object' => 'Дуплекс', 'action' => 'rent'], 'rieltprof-duplexctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-tounhouses.svg" alt=""></div><div class="text-holder"><span>Дуплекс</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 11, 'rent_dir' => 21, 'object' => 'Гараж', 'action' => 'rent'], 'rieltprof-garagectrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-garage.svg" alt=""></div><div class="text-holder"><span>Гараж</span></div></a></li>
                    <li><a class="crud-add" href="{$router->getAdminUrl('add', ['referer' => $referer, 'sale_dir' => 12, 'rent_dir' => 22, 'object' => 'Коммерция', 'action' => 'rent'], 'rieltprof-commercialctrl')}"><div class="icon-holder"><img src="{$THEME_IMG}/categories-icons/black/icon-commerce.svg" alt=""></div><div class="text-holder"><span>Коммерция</span></div></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>
