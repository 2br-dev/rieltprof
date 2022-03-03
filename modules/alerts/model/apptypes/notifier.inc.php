<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Alerts\Model\AppTypes;

/**
* Desktop приложение, отображающее различные уведомления из интернет-магазина
*/
class Notifier extends \ExternalApi\Model\App\AbstractAppType
{
    /**
    * Возвращает  строковый идентификатор приложения
    * 
    * @return string
    */
    public function getId()
    {
        return 'notifyapp';
    }
    
    /**
    * Возвращает SHA1 от секретного ключа client_secret, который должен 
    * передаваться вместе с client_id в момент авторизации
    * 
    * @return string
    */
    public function checkSecret($client_secret)
    {
        return sha1( $client_secret ) == 'f8ebe8f6e99f89d126554d088b46cbdcb67c76a9';
    }
    
    /**
    * Метод возвращает название приложения
    * 
    * @return string
    */
    public function getTitle()
    {
        return t('Desktop приложение для уведомлений');
    }
    
    /**
    * Метод возвращает массив, содержащий требуемые права доступа к json api для приложения
    * 
    * @return [
    *   [
    *       'method' => 'метод',
    *       'right_codes' => [код действия, код действия, ...]
    *   ],
    *   ...
    * ]
    */
    public function getAppRights()
    {
        return [
            'notice.getNewList' => self::FULL_RIGHTS,
            'notice.getTypeList' => self::FULL_RIGHTS
        ];
    }

    /**
    * Возвращает группы пользователей, которым доступно данное приложение.
    * Сведения загружаются из настроек текущего модуля
    * 
    * @return ["group_id_1", "group_id_2", ...]
    */    
    public function getAllowUserGroup()
    {
        return \RS\Config\Loader::byModule($this)->allow_user_groups;
    }
    
}
