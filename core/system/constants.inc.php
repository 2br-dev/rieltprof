<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

/**
* Здесь определяются глобальные константы
*/

// \RS\Http\Request
define('TYPE_STRING','string');
define('TYPE_INTEGER','integer');
define('TYPE_ARRAY','array');
define('TYPE_BOOLEAN','boolean');
define('TYPE_FLOAT','float');
define('TYPE_OBJECT','object');
define('TYPE_MIXED','');

define('REQUEST', '0');
define('POST', '1');
define('GET', '2');
define('FILES', '3');
define('COOKIE', '4');
define('SERVER', '5');
define('PARAMETERS', '6');


//Константы, испоьзуемые в классах проверки прав доступа (musers, menu,...)
define('FULL_USER_ACCESS',-1); //Полный доступ к меню пользователя
define('FULL_ADMIN_ACCESS',-2); //Полный доступ к меню администратора
define('FULL_MODULE_ACCESS','all'); //Полный доступ к модулям

define('DEBUG_MODE', 'DEBUGMODE'); //Ключ для режима отладки в $_SESSION

//Константы для HTML_TABLE
define ('SORTABLE_NONE', false);
define ('SORTABLE_ASC', 'ASC');
define ('SORTABLE_DESC', 'DESC');
define ('SORTABLE_BOTH', 'BOTH');

//Base Path - базовый путь к ресурсам
define ('BP_ROOT', 'root'); //от корня
define ('BP_COMMON', 'common'); //от папи с ресурсами
define ('BP_THEME', 'theme'); //от папки с ресурсами темы в клиентской части

// @deprecated (08.18) Константы для описывающие стандартные биты прав доступа
define('ACCESS_BIT_READ', 0);
define('ACCESS_BIT_WRITE', 1);

//Контанты для стандартных тегов кэша
define('CACHE_TAG_MODULE', 'modules'); //При добвлении, удалении модуля кэш по данному тегу инвалидируется
define('CACHE_TAG_SITE', 'sites'); //При добвлении, удалении сайта кэш по данному тегу инвалидируется
define('CACHE_TAG_BLOCK_PARAM', 'block_params'); //При изменении параметров блоков, кэш по данному тегу удаляется
define('CACHE_TAG_SITE_UPDATE', 'site_update'); //После обновления системы, кэш по данному тегу удаляется
define('CACHE_TAG_UPDATE_CATEGORY', 'update_category'); //После обновления системы, кэш по данному тегу удаляется

//Константа для поддержки WebP (IMAGETYPE_WEBP доступен только в 7.1)
if (!defined('IMAGETYPE_WEBP')) {
    define('IMAGETYPE_WEBP', 18);
}