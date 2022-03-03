<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

use RS\Exception;

/**
 * Абстрактный итератор древовидного списка
 */
abstract class AbstractTreeListIterator implements \Iterator, \ArrayAccess, \Countable
{
    const ATTRIBUTE_MULTIPLE = 'multiple';
    const ATTRIBUTE_DISALLOW_SELECT_BRANCHES = 'disallowSelectBranches';

    /** @var AbstractTreeListNode[] */
    protected $items = null;
    /** @var AbstractTreeListNode[] */
    protected $first_elements = [];

    /**
     * Возвращает список элементов
     *
     * @return AbstractTreeListNode[]
     */
    public final function getItems()
    {
        $this->loadList();
        return $this->items;
    }

    /**
     * \Iterator
     * Возвращает текущий элемент
     *
     * @return AbstractTreeListNode
     */
    public final function current()
    {
        $this->loadList();
        return current($this->items);
    }

    /**
     * \Iterator
     * Перемещает указатель на следуйщий элемент
     *
     * @return void
     */
    public final function next()
    {
        $this->loadList();
        next($this->items);
    }

    /**
     * \Iterator
     * Возвращает ключ текущего элемента
     *
     * @return int|null
     */
    public final function key()
    {
        $this->loadList();
        return key($this->items);
    }

    /**
     * \Iterator
     * Проверяет корректность текущей позиции
     *
     * @return bool
     */
    public final function valid()
    {
        $this->loadList();
        if (key($this->items) === null) {
            $this->unsetList();
            return false;
        }
        return true;
    }

    /**
     * \Iterator
     * Перемещает указатель на первый элемент
     *
     * @return void
     */
    public final function rewind()
    {
        $this->loadList();
        reset($this->items);
    }

    /**
     * \ArrayAccess
     * Проверяет наличие значения по ключу
     *
     * @param mixed $offset - ключ
     * @return bool
     */
    public final function offsetExists($offset)
    {
        $this->loadList();
        return isset($this->items[$offset]);
    }

    /**
     * \ArrayAccess
     * Возвращает значение по ключу
     *
     * @param mixed $offset - ключ
     * @return AbstractTreeListNode|null
     */
    public final function offsetGet($offset)
    {
        $this->loadList();
        if (isset($this->items[$offset])) {
            return $this->items[$offset];
        }
        return null;
    }

    /**
     * \ArrayAccess
     * Устанавливает значение по ключу
     *
     * @param mixed $offset - ключ
     * @param AbstractTreeListNode $value - значение
     * @return void
     * @throws Exception
     */
    public final function offsetSet($offset, $value)
    {
        if ($value instanceof AbstractTreeListNode) {
            $this->items[$offset] = $value;
        } else {
            throw new Exception(t('Итератор древовидного списка может содержать только наследников AbstractTreeListNode'));
        }
    }

    /**
     * \ArrayAccess
     * Удаляет значение по ключу
     *
     * @param mixed $offset - ключ
     * @return void
     */
    public final function offsetUnset($offset)
    {
        if (isset($this->items[$offset])) {
            unset($this->items[$offset]);
        }
    }

    /**
     * \Countable
     * Возвращает количество элементов
     *
     * @return int
     */
    public final function count()
    {
        $this->loadList();
        return count($this->items);
    }

    /**
     * Формирует итоговый список дочерних элементов
     *
     * @return void
     */
    protected final function loadList()
    {
        if ($this->items === null) {
            $this->items = array_merge($this->getFirstElements(), $this->getSelfItems());
        }
    }

    /**
     * Возвращает список узлов, составляющих путь к указанному элементу от корня
     *
     * @param string|int $node_id - идентификатор целевого узла
     * @return AbstractTreeListNode[]
     */
    public abstract function getPathFromRoot($node_id): array;

    /**
     * Возвращает список дочерних элементов
     *
     * @return AbstractTreeListNode[]
     */
    protected abstract function getSelfItems();

    /**
     * Возвращает список узлов, которые будет добавлен в начало списка дочерних элементов
     *
     * @return AbstractTreeListNode[]
     */
    public function getFirstElements()
    {
        return $this->first_elements;
    }

    /**
     * Устанавливает список узлов, которые будет добавлен в начало списка дочерних элементов
     *
     * @param AbstractTreeListNode[] $first_elements - узлы, которые будет добавлен в начало списка детей
     * @return void
     * @throws Exception
     */
    public function setFirstElements(array $first_elements)
    {
        foreach ($first_elements as $item) {
            if (!($item instanceof AbstractTreeListNode)) {
                throw new Exception(t('Итератор древовидного списка может содержать только наследников AbstractTreeListNode'));
            }
        }
        $this->first_elements = $first_elements;
    }

    /**
     * Очищает список дочерних элементов
     *
     * @return void
     */
    protected final function unsetList()
    {
        $this->items = null;
    }
}
