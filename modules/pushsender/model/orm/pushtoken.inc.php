<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace PushSender\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект связи пользователя и токена Firebase для отправки Push уведомлений.
 * Клиентское устройство должно зарегистрироваться в базе Firebase Cloud Messaging и получить token.
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $user_id ID пользователя
 * @property string $push_token Токен пользователя в Firebase
 * @property string $dateofcreate Дата создания
 * @property string $app Приложение, для которого выписан token
 * @property string $uuid Уникальный идентификатор устройства
 * @property string $model Модель устройства
 * @property string $manufacturer Производитель
 * @property string $platform Платформа на устройстве
 * @property string $version Версия платформы на устройстве
 * @property string $cordova Версия cordova js
 * @property string $ip IP адрес
 * --\--
 */
class PushToken extends \RS\Orm\OrmObject
{
    protected static
        $table = 'pushsender_user_token';
    
    function _init()
    {
        parent::_init()->append([
            'user_id' => new Type\Integer([
                'description' => t('ID пользователя'),
            ]),
            'push_token' => new Type\Varchar([
                'description' => t('Токен пользователя в Firebase'),
                'maxlength' => 300,
            ]),
            'dateofcreate' => new Type\Datetime([
                'description' => t('Дата создания')
            ]),
            'app' => new Type\Varchar([
                'description' => t('Приложение, для которого выписан token'),
                'maxLength' => 50
            ]),
            'uuid' => new Type\Varchar([
                'description' => t('Уникальный идентификатор устройства'),
                'maxLength' => 255,
            ]),
            'model' => new Type\Varchar([
                'maxLength' => 80,
                'description' => t('Модель устройства')
            ]),
            'manufacturer' => new Type\Varchar([
                'maxLength' => 80,
                'description' => t('Производитель')
            ]),
            'platform' => new Type\Varchar([
                'maxLength' => 50,
                'description' => t('Платформа на устройстве')
            ]),
            'version' => new Type\Varchar([
                'description' => t('Версия платформы на устройстве')
            ]),
            'cordova' => new Type\Varchar([
                'description' => t('Версия cordova js')
            ]),
            'ip' => new Type\Varchar([
                'maxLength' => 20,
                'description' => t('IP адрес'),
            ]),
        ]);
        
        $this->addIndex(['model', 'platform'], self::INDEX_KEY);
        $this->addIndex(['user_id', 'push_token'], self::INDEX_UNIQUE);
        $this->addIndex(['app', 'uuid'], self::INDEX_UNIQUE);
    }
}
