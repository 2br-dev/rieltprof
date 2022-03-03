<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Alerts\Config;
use \RS\Orm\Type;

/**
* Конфигурационный файл модуля
*/
class File extends \RS\Orm\ConfigObject
{

    function _init()
    {
        parent::_init()->append([
            t('Основные'),
                'sms_sender_class' => new Type\Varchar([
                    'description' => t('SMS провайдер'),
                    'List' => [[new \Alerts\Model\Api(), 'selectSendersList']],
                ]),
                'sms_sender_login' => new Type\Varchar([
                    'description' => t('Логин'),
                ]),
                'sms_sender_pass' => new Type\Varchar([
                    'description' => t('Пароль'),
                ]),
            t('Desktop уведомления'),
                'notice_items_delete_hours' => new Type\Integer([
                    'description' => t('Количество часов, которое следует хранить уведомления')
                ]),
                'allow_user_groups' => new Type\ArrayList([
                    'runtime' => false,            
                    'description' => t('Группы пользователей, для которых доступно данное приложение'),
                    'list' => [['\Users\Model\GroupApi','staticSelectList']],
                    'size' => 7,
                    'attr' => [['multiple' => true]]
                ])
        ]);

    }
       
    /**
    * Возвращает значения свойств по-умолчанию
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return 
            parent::getDefaultValues() + [
                'tools' => [
                    [
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('ajaxTestSms', [], 'alerts-ctrl'),
                        'title' => t('Отправить тестовое SMS-сообщение'),
                        'description' => t('Отправляет SMS-сообщение, на номер администратора, указанный в настройках сайта'),
                        'confirm' => t('Вы действительно хотите отправить тестовое SMS-сообщение')
                    ],
                    [
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('showSmsLog', [], 'alerts-tools'),
                        'title' => t('Открыть журнал отправки SMS'),
                        'description' => t('Отображает все попытки и результаты отправки SMS, если включено логирование'),
                        'class' => ' '
                    ],
                    [
                        'url' => \RS\Router\Manager::obj()->getAdminUrl('deleteSmsLog', [], 'alerts-tools'),
                        'title' => t('Очистить журнал отправки SMS'),
                        'description' => t('Очистить журнал запросов к API?'),
                    ],
                ]
            ];
    }
}
