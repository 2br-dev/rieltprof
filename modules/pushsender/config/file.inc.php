<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Config;
use \RS\Orm\Type;

class File extends \RS\Orm\ConfigObject
{
    function _init()
    {
        parent::_init()->append([
            'googlefcm_server_key' => new Type\Varchar([
                'description' => t('Ключ сервера Google FireBase Cloud Messaging'),
                'hint' => t('Ключ из настроек в Google FireBase Cloud Messaging'),
            ]),
            'enable_log' => new Type\Integer([
                'description' => t('Включить логирование'),
                'hint' => t('Используйте данную функцию только для отладки'),
                'checkboxView' => [1,0]
            ])
        ]);
    }
    
    /**
    * Возвращает массив кнопок для панели справа
    * 
    * @return array
    */
    public static function getDefaultValues()
    {
        return parent::getDefaultValues() + [
            'tools' => [
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('readLog', [], 'pushsender-tools'),
                    'title' => t('Просмотреть log-файл'),
                    'description' => t('Открывает в новой вкладке файл с технической информацией об отправке push уведомлений, если включено логирование'),
                    'class' => ' '
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl('clearLog', [], 'pushsender-tools'),
                    'title' => t('Очистить log-файл'),
                    'description' => t('Очищает log-файл'),
                ],
                [
                    'url' => \RS\Router\Manager::obj()->getAdminUrl(false, [], 'pushsender-pushtokenctrl'),
                    'title' => t('Просмотреть push-токены'),
                    'description' => t('Отображает зарегистрированные токены клиентских устройств'),
                    'class' => ' '
                ]
            ]
            ];
    }

    /**
     * Возвращает список пунктов меню
     * @return array
     */
    function getMenusList()
    {
        return \Menu\Model\Api::staticSelectList();
    }
    
}