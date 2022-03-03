<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Main\Config;

use RS\Http\Request as HttpRequest;
use RS\Module\Exception as ModuleException;
use RS\Orm\ConfigObject;
use RS\Orm\Type;
use RS\Router\Manager as RouterManager;

/**
 * Конфигурационный файл модуля
 * @ingroup Main
 */
class File extends ConfigObject
{
    function _init()
    {
        parent::_init();
        $this->getPropertyIterator()->append([
            t('Изображения'),
                'image_quality' => new Type\Integer([
                    'description' => t('Качество генерируемых фото (от 0 до 100). 100 - самое лучшее.'),
                ]),
                'watermark' => new Type\File([
                    'maxLength' => '255',
                    'max_file_size' => 10000000,
                    'allow_file_types' => ['image/png'],
                    'description' => t('Водяной знак (PNG)'),
                    'storage' => [\Setup::$ROOT, \Setup::$STORAGE_DIR.'/watermark/']
                ]),
                'wmark_min_width' => new Type\Integer([
                    'description' => t('Минимальная ширина изображения на которую будет установлен водяной знак'),
                    'Attr' => [['size' => '4']],
                ]),
                'wmark_min_height' => new Type\Integer([
                    'description' => t('Минимальная высота изображения на которую будет установлен водяной знак'),
                    'Attr' => [['size' => '4']],
                ]),
                'wmark_pos_x' => new Type\Varchar([
                    'maxLength' => '10',
                    'description' => t('Позиция по горизонтали'),
                    'Attr' => [['size' => '1']],
                    'ListFromArray' => [['left' => t('Слева'), 'center' => t('По центру'), 'right' => t('Справа')]],
                ]),
                'wmark_pos_y' => new Type\Varchar([
                    'maxLength' => '10',
                    'description' => t('Позиция по вертикали'),
                    'Attr' => [['size' => '1']],
                    'ListFromArray' => [['top' => t('Сверху'), 'middle' => t('По центру'), 'bottom' => t('Снизу')]],
                ]),
                'wmark_opacity' => new Type\Integer([
                    'description' => t('Процент непрозрачности водяного знака при наложении. от 1 до 100. 100 - водяной знак будет наложен как есть'),
                    'Attr' => [['size' => '4']],
                ]),
                'webp_generate_only' => new Type\Integer([
                    'description' => t('Генерировать для сайта миниатюры в формате WebP?'),
                    'checkboxView' => [1, 0],
                    'hint' => 'Для работы опции необходимо, чтобы PHP был собран с поддержкой WebP.',
                ]),
                'webp_disable_on_apple' => new Type\Integer([
                    'description' => t('Не использовать WebP на устройствах Apple'),
                    'checkboxView' => [1, 0],
                ]),
            t('CSV импорт/экспорт'),
                'csv_charset' => new Type\Varchar([
                    'description' => t('Кодировка CSV файлов'),
                    'listFromArray' => [[
                        'utf-8' => 'UTF-8',
                        'windows-1251' => 'WINDOWS-1251',
                    ]]
                ]),
                'csv_delimiter' => new Type\Varchar([
                    'description' => t('Разделитель'),
                    'listFromArray' => [[
                        ';' => t('; (точка с запятой)'),
                        ',' => t(', (запятая)')
                    ]]
                ]),
                'csv_check_timeout' => new Type\Integer([
                    'description' => t('Использовать пошаговую загрузку?'),
                    'maxLength' => 1,
                    'default' => 1,
                    'CheckboxView' => [1, 0],
                ]),
                'csv_timeout' => new Type\Integer([
                    'description' => t('Время одного шага импорта'),
                    'maxLength' => 11,
                    'default' => 26,
                ]),
            t('Геолокация'),
                'geo_ip_service' => new Type\Varchar([
                    'description' => t('Сервис для определения ближайшего филиала по IP'),
                    'list' => [['Main\Model\GeoIpApi', 'getGeoIpServicesName']]
                ]),
                'dadata_token' => new Type\Varchar([
                    'description' => t('Ключ API от DaData.ru'),
                    'hint' => t('Зарегистрируйтесь и получите ключ на сайте DaData.ru'),
                    'template' => '%main%/form/config/dadata_token.tpl'
                ]),
            t('Сервер событий'),
                'long_polling_can_enable' => new Type\Integer([
                    'description' => t('Разрешить включение сервера событий'),
                    'hint' => t('Сервер событий позволяет держать "долгое" соединение между браузером администратора и сервером и мгновенно доставлять события в браузер.')
                ]),
                'long_polling_timeout_sec' => new Type\Integer([
                    'description' => t('Максимальное время соединения с сервером одного потока'),
                    'hint' => t('Сервер будет держать соединение с браузером заданное количество секунд, если не произойдет ни одно событие. После обрыва, соединение опять устанавливается.'),
                    'listFromArray' => [[
                        '5' => '5 секунд',
                        '10' => '10 секунд',
                        '20' => '20 секунд',
                        '25' => '25 секунд'
                    ]]
                ]),
                'long_polling_event_listen_interval_sec' => new Type\Integer([
                    'description' => t('Интервал запроса новых событий на сервере'),
                    'listFromArray' => [[
                        '1' => '1 секунда',
                        '2' => '2 секунды',
                        '3' => '3 секунды',
                        '4' => '4 секунды'
                    ]],
                ]),

            t('Сервисы Яндекса'),
                'yandex_services_hint' => (new Type\ArrayList())
                    ->setDescription(t('Помощь по использованию сервисов'))
                    ->setTemplate('%main%/form/config/yandex_help.tpl'),
                'yandex_js_api_geocoder' => (new Type\Varchar())
                    ->setDescription(t('Ключ для "JavaScript API и HTTP Геокодера"')),

            t('Сервис DaData'),
                'dadata_service_hint' => (new Type\ArrayList())
                    ->setDescription(t('Помощь по использованию сервисов'))
                    ->setTemplate('%main%/form/config/dadata_help.tpl'),
                'dadata_api_key' => (new Type\Varchar())
                    ->setDescription(t('API-ключ')),
                'dadata_secret_key' => (new Type\Varchar())
                    ->setDescription(t('Секретный ключ'))
                    ->setHint(t('Испльзуется при стандартизации адреса')),
                'dadata_enable_log' => (new Type\Integer())
                    ->setDescription(t('Вести лог запросов'))
                    ->setMaxLength(1)
                    ->setCheckboxView(1, 0)
                    ->setDefault(0),

            t('Поддержка ReadyScript'),
                'enable_remote_support' => (new Type\Integer())
                    ->setDescription(t('Разрешить удаленный доступ в административную панель технической поддержке ReadyScript'))
                    ->setHint(t('При обращении в поддержку, вам не нужно будет создавать и сообщать временный логин и пароль для сотрудников поддержки.'))
                    ->setCheckboxView(1, 0),
        ]);

        if (!function_exists('imagewebp')) {
            $this['__webp_generate_only']->setAttr(['disabled' => true]);
        }
    }

    /**
     * Возвращает значения свойств по-умолчанию
     *
     * @return array
     * @throws ModuleException
     */
    public static function getDefaultValues()
    {
        $router = RouterManager::obj();

        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => $router->getAdminUrl('CreateLangFilesDialog', [], 'main-lang'),
                    'title' => t('Создание языковых файлов'),
                    'description' => t('Создает в каждом модуле и теме оформления файлы локализации, для перевода на другие языки'),
                    'class' => 'crud-add crud-sm-dialog',
                ],
                [
                    'url' => $router->getAdminUrl('ajaxRecalculatePositions', [], 'main-widgets'),
                    'title' => t('Исправить позиции виджетов'),
                    'description' => t('Пересчитывает сортировочные индексы виджетов. Необходимо вызывать, если наблюдаются проблемы с сортировкой виджетов.'),
                ],
                [
                    'url' => $router->getAdminUrl(false, [], 'main-externalrequestcachecontrol'),
                    'title' => t('Кэш внешних запросов'),
                    'description' => t('Отображает записи кэша внешних запросов, позволяет очистить кэш.'),
                    'class' => ' ',
                ],
                [
                    'url' => $router->getAdminUrl(false, [], 'main-externalrequestlogviewer'),
                    'title' => t('Логи внешних запросов'),
                    'description' => t('Позволяет просмотреть лог-файлы внешних запросов'),
                    'class' => 'crud-add crud-sm-dialog',
                ],
            ]
            ];
    }

    /**
     * Проверяет клиентское устройство на соответствие Apple
     *
     * @return bool
     */
    public function isAppleUser()
    {
        $user_agent = HttpRequest::commonInstance()->server('HTTP_USER_AGENT', TYPE_STRING);
        return (bool)preg_match('/Mac OS X/', $user_agent);
    }
}
