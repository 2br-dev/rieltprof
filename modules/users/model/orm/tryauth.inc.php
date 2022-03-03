<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Users\Model\Orm;
use \RS\Orm\Type;

/**
 * Объект - связь пользователей с группами
 * --/--
 * @property string $ip IP-адрес
 * @property integer $total Количество попыток авторизации
 * @property string $last_try_dateof Дата последней попытки авторизации
 * @property string $try_login Логин, последней попытки авторизации
 * --\--
 */
class TryAuth extends \RS\Orm\AbstractObject
{
    protected static
        $table = "try_auth";

    protected function _init()
    {
        $properties = $this->getPropertyIterator()->append([
            'ip' => new Type\Varchar([
                'description' => t('IP-адрес'),
                'primaryKey' => true
            ]),
            'total' => new Type\Integer([
                'description' => t('Количество попыток авторизации')
            ]),
            'last_try_dateof' => new Type\Datetime([
                'description' => t('Дата последней попытки авторизации')
            ]),
            'try_login' => new Type\Varchar([
                'description' => t('Логин, последней попытки авторизации')
            ])
        ]);
    }
    
    function _initDefaults()
    {
        $this->setLocalParameter('checkRights', false);
    }
    
    function getPrimaryKeyProperty()
    {
        return 'ip';
    }
}