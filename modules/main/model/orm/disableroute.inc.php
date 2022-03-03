<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Main\Model\Orm;

use RS\Orm\AbstractObject;
use RS\Orm\Request;
use RS\Orm\Type;
use RS\Cache\Manager;
use RS\Exception;

/**
 * Таблица хранит сведения об отключенных маршрутах
 */
class DisableRoute extends AbstractObject
{
    protected static $table = 'disabled_routes';
    /**
     * В данном методе должны быть заданы поля объекта.
     * Вызывается один раз для одного класса объектов в момент первого обращения к свойству
     */
    protected function _init()
    {
        $this->getPropertyIterator()->append([
            'route_id' => new Type\Varchar([
                'primaryKey' => true,
                'description' => t('ID отключенного маршрута')
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
     * @return string
     */
    public function getPrimaryKeyProperty()
    {
        return 'route_id';
    }

    /**
     * Возвращает полный список отключенных маршрутов
     *
     * @param bool $cache Если true, то будет использовано кэширование
     * @return mixed
     */
    public static function getDisabledRoutes($cache = true)
    {
        if ($cache) {
            return Manager::obj()
                ->expire(0)
                ->watchTables(new self())
                ->request([__CLASS__, __FUNCTION__], false);
        } else {
            try {
                return Request::make()
                    ->from(self::_getTable())
                    ->exec()
                    ->fetchSelected('route_id', 'route_id');
            } catch (Exception $e) { //Реализовано в целях корректного обновления, пока не обновлена база
                return [];
            }
        }
    }

    /**
     * Возвращает true, если маршрут можно отключать
     *
     * @param string $id
     * @return bool
     */
    public function canDisableRoute($id)
    {
        $id = strtolower($id);
        $exist_routes = array_keys(\RS\Router\Manager::getRoutes());
        return in_array($id, $exist_routes) && !in_array($id, [
            'main.admin'
        ]);
    }
}