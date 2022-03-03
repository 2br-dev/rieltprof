<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

use RS\Orm\AbstractObject;

/**
 * Абстрактный узел древовидного списка
 */
abstract class AbstractTreeListNode implements \ArrayAccess
{
    const ARRAY_ACCESS_KEY_OBJECT = 'fields';
    const ARRAY_ACCESS_KEY_CHILDS = 'child';
    const DEFAULT_FIELD_ID = 'id';
    const DEFAULT_FIELD_NAME = 'name';

    /** @var AbstractObject|array */
    protected $object;
    /** @var AbstractTreeListIterator */
    protected $childs = null;
    /** @var int */
    protected $child_count = null;

    /**
     * AbstractTreeListNode constructor.
     *
     * @param object|array $object - содержимое узла
     */
    public function __construct($object)
    {
        $this->setObject($object);
    }

    /**
     * Возвращает является ли ветка дерева развёрнутой
     *
     * @return bool
     */
    public function isOpened()
    {
        return false;
    }

    /**
     * Возвращает количество дочерних узлов
     *
     * @return int
     */
    public function getChildsCount()
    {
        if ($this->child_count === null) {
            return count($this->getChilds());
        } else {
            return $this->child_count;
        }
    }

    /**
     * Устанавливает количество дочерних узлов
     *
     * @param int $count - количество дочерних узлов
     */
    public function setChildsCount($count)
    {
        $this->child_count = $count;
    }

    /**
     * Возвращает итератор со списком дочерних элементов
     *
     * @return AbstractTreeListIterator
     */
    public function getChilds()
    {
        if ($this->childs === null) {
            $this->childs = $this->getSelfChilds();
        }
        return $this->childs;
    }

    /**
     * Устанавливает итератор со списком дочерних элементов
     *
     * @param AbstractTreeListIterator $iterator - итератор со списком дочерних элементов
     */
    public function setChilds(AbstractTreeListIterator $iterator)
    {
        $this->childs = $iterator;
    }

    /**
     * Возвращает свой итератор со списком дочерних элементов
     *
     * @return AbstractTreeListIterator
     */
    abstract public function getSelfChilds();

    /**
     * Возвращает идентификатор узла
     *
     * @return string|int
     */
    public function getID()
    {
        $object = $this->getObject();
        return $object[self::DEFAULT_FIELD_ID];
    }

    /**
     * Возвращает имя узла
     *
     * @return string
     */
    public function getName()
    {
        $object = $this->getObject();
        return $object[self::DEFAULT_FIELD_NAME];
    }

    /**
     * Возвращает объект узла
     *
     * @return AbstractObject|array
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Устанавливает объект узла
     *
     * @param AbstractObject|array $object - orm объект
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * \ArrayAccess
     * Проверяет наличие значения по ключу
     * существующими считаем только orm объект и его потомков по дереву
     *
     * @param string $offset - ключ
     * @return bool
     */
    public final function offsetExists($offset)
    {
        return in_array($offset, [self::ARRAY_ACCESS_KEY_OBJECT, self::ARRAY_ACCESS_KEY_CHILDS]);
    }

    /**
     * \ArrayAccess
     * Возвращает значение по ключу
     * получить можно только orm объект и его потомков по дереву
     *
     * @param string $offset - ключ
     * @return mixed
     */
    public final function offsetGet($offset)
    {
        if ($offset == self::ARRAY_ACCESS_KEY_OBJECT) {
            return $this->getObject();
        } elseif ($offset == self::ARRAY_ACCESS_KEY_CHILDS) {
            return $this->getChilds();
        }
        return null;
    }

    /**
     * \ArrayAccess
     * Устанавливает значение по ключу
     * после создания объекта нельзя подменить его содержимое
     *
     * @param string $offset - ключ
     * @param mixed $value - значение
     * @return void
     */
    public final function offsetSet($offset, $value)
    {
    }

    /**
     * \ArrayAccess
     * Удаляет значение по ключу
     * после создания объекта нельзя подменить его содержимое
     *
     * @param string $offset - ключ
     * @return void
     */
    public final function offsetUnset($offset)
    {
    }
}
