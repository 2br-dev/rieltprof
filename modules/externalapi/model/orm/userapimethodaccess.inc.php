<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * ORM Объект - доступ к методу API для авторизованного пользователя
 * --/--
 * @property integer $site_id ID сайта
 * @property integer $user_id Пользователь
 * @property string $api_method Имя метода API
 * --\--
 */
class UserApiMethodAccess extends AbstractObject
{
    protected static
        $table = 'external_api_user_allow_methods';
    /**
     * В данном методе должны быть заданы поля объекта.
     * Вызывается один раз для одного класса объектов в момент первого обращения к свойству
     */
    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'site_id' => new Type\CurrentSite(),
            'user_id' => new Type\User([
                'description' => t('Пользователь')
            ]),
            'api_method' => new Type\Varchar([
                'description' => t('Имя метода API')
            ])
        ]);
    }

    /**
     * Возвращает имя свойства, которое помечено как первичный ключ.
     * Для совместимости с предыдущими версиями, метод ищет первичный ключ в свойствах.
     *
     * С целью увеличения производительности необходимо у наследников реализовать явное
     * возвращение свойств, отвечающих за первичный ключ.
     *
     * @return array
     */
    public function getPrimaryKeyProperty()
    {
        return [
            'site_id',
            'user_id',
            'api_method'
        ];
    }
}