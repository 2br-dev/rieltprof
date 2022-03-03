<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace AtolOnline\Config;
use \RS\Orm\Type;
use Shop\Model\CashRegisterApi;


/**
* Описание файла конфига модуля
*/
class File extends \RS\Orm\ConfigObject
{ 
    function _init()
    {
        parent::_init()->append([
            'service_url' => new Type\Varchar([
                'description' => t('URL API'),
                'hint' => t('Допустимо пустое поле. Пустое поле означает, что будет использоваться стандартный URL для обмена с сервисом АТОЛ.ОНЛАЙН')
            ]),
            '_load_settings' => new Type\Varchar([
                'description' => '',
                'template' => '%atolonline%/load_settings.tpl'
            ]),
            'login' => new Type\Varchar([
                'description' => t('Логин'),
                'hint' => t('Выдаётся АТОЛ')
            ]),
            'pass' => new Type\Varchar([
                'description' => t('Пароль'),
                'hint' => t('Выдаётся АТОЛ'),
                'attr' => [[
                    'type' => 'password'
                ]]
            ]),
            'group_code' => new Type\Varchar([
                'description' => t('Группа'),
                'hint' => t('Выдаётся АТОЛ')
            ]),
            'inn' => new Type\Varchar([
                'description' => t('ИНН организации'),

            ]),
            'sno' => new Type\Varchar([
                'description' => t('Система налогообложения'),
                'hint' => t('Не обязательно, если у организации один тип налогооблажения'),
                'list' => [['Shop\Model\CashRegisterApi', 'getStaticSnoList'], [0 => t('-Не выбрано-')]],
            ]),
            'domain' => new Type\Varchar([
                'description' => t('Доменное имя вашего магазина (как оно указано в АТОЛ)'),
                'hint' => t('Если не указано, то будет использоваться доменное имя без протокола, например: yourstore.com'),
            ]),
            'api_version' => new Type\Varchar([
                'description' => t('Версия протокола АТОЛ'),
                'hint' => t('Выберите версию протокола исходя из формата фискальных данных, указанных в кабинете АТОЛ'),
                'listFromArray' => [[
                    '3' => 'Версия 3 (ФФД 1.0)',
                    '4' => 'Версия 4 (ФФД 1.05)'
                ]]
            ])
        ]);
    }
    
    /**
    * Возвращает список действий для панели конфига
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('checkAuth', [], 'atolonline-tools'),
                    'title' => t('Проверить авторизацию'),
                    'description' => t('Делает запрос на авторизацию и возвращает результат'),
                ],
                [
                    'url' => 'https://online.atol.ru/lk/Account/Register?partnerUid=81fa9c14-6ddb-4aee-90bb-f161278e72e9',
                    'title' => t('Создать аккаунт в АТОЛ.ONLINE'),
                    'description' => t('Перейдите по ссылке, если вам необходимо создать аккаунт на сервисе АТОЛ.ONLINE'),
                    'class' => 'partner-link',
                ]
            ]
            ];
    }  
}