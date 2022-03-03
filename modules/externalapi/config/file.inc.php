<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            t('Основные'),
            'allow_domain' => new Type\Varchar([
                'description' => t('Разрешить работу API только на следующем домене'),
                'hint' => t('Если у вас несколько зеркал сайта, то вы можете разрешить пользоваться API только на одном из них, например api.yourdomain.ru, при обращении к API на других доменах будет 404 ошибка')
            ]),
            'api_key' => new Type\Varchar([
                'description' => t('API ключ(придумайте его)'),
                'hint' => t('Может состоять из цифр, английских букв и знаков "минус", "подчеркивание".Данный ключ будет использоваться в URL для API. (/api-{КЛЮЧ}/methods/...). Рекомендуется его задавать для усиления безопасности и сокрытия API от посторонних пользователей.'),
                'checker' => ['ChkAlias', 'Недопустимые символы в API ключе']
            ]),
            'enable_api_help' => new Type\Integer([
                'description' => t('Включить возможность видеть справку по внешнему API по ссылке /api-[API ключ]/help'),
                'checkboxView' => [1,0]
            ]),
            'show_internal_error_details' => new Type\Integer([
                'description' => t('Отображать детальную информацию по внутренним ошибкам при вызове API.'),
                'checkboxView' => [1,0]
            ]),
            'token_lifetime' => new Type\Integer([
                'description' => t('Время жизни авторизационного токена в секундах'),
            ]),
            'default_api_version' => new Type\Varchar([
                'description' => t('Версия API по умолчанию')
            ]),
            'enable_request_log' => new Type\Integer([
                'description' => t('Включить логирование запросов'),
                'hint' => t('Включайте исключительно на время отладки. Не держите постоянно включенным на production сервере'),
                'checkboxView' => [1,0]
            ]),
            'disable_image_webp' => new Type\Integer([
                'description' => t('Отключить webp для внешних запросов'),
                'hint' => t('Необходимо для работы мобильного приложения. Актуально, только если в настройках модуля "Системный модуль" включена опция "Генерировать для сайта миниатюры только в формате WebP?"'),
                'checkboxView' => [1,0]
            ]),
            t('Разрешенные методы API'),
            'allow_api_methods' => new Type\ArrayList([
                'runtime' => false,
                'description' => t('Разрешенные методы API'),
                'hint' => t('В справке будет отображаться информация только о разрешенных методах API, также вызвать можно будет только отмеченные здесь'),
                'list' => [['ExternalApi\Model\ApiRouter', 'getApiMethodsSelectList'], ['all' => 'Все']],
                'checkboxListView' => true,
                'template' => '%externalapi%/form/config/allow_api_methods.tpl'
            ])
        ]);
    }

    /**
     * Возращает массив JSON со списком включенных модулей
     *
     * @return bool
     */
    function isShopModuleEnabled()
    {
        return (\RS\Module\Manager::staticModuleExists('shop') && \RS\Module\Manager::staticModuleEnabled('shop'));
    }
    
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl(false, [], 'externalapi-logctrl'),
                    'title' => t('Журнал запросов к API'),
                    'description' => t('Отображает все запросы к API и ответы сервера, если включено логирование'),
                    'class' => ' '
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl(false, [], 'externalapi-authtokenctrl'),
                    'title' => t('Авторизационные токены'),
                    'description' => t('Отображает все имеющиеся авторизационные токены на текущий момент'),
                    'class' => ' '
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl(false, ['do' => 'clearLog'], 'externalapi-logctrl'),
                    'title' => t('Очистить журнал запросов к API'),
                    'description' => t('Очистить журнал запросов к API?'),
                ],
            ]
            ];
    }   
}
