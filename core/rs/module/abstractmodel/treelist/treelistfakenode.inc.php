<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/ declare(strict_types=1);

namespace RS\Module\AbstractModel\TreeList;

/**
 * Несуществующий узел древовидного списка
 */
class TreeListFakeNode extends AbstractTreeListNode
{
    protected $name_field = self::DEFAULT_FIELD_NAME;

    /**
     * Возвращает имя узла
     *
     * @return string
     */
    public function getName(): string
    {
        $object = $this->getObject();
        /** @var string $name */
        $name = $object[$this->name_field];
        return $name;
    }

    /**
     * Устанавливает какой ключ в массиве объекта считать именем узла
     *
     * @param string $field_name - имя ключа
     * @return void
     */
    public function setNameField(string $field_name): void
    {
        $this->name_field = $field_name;
    }

    /**
     * Возвращает итератор со списком дочерних элементов
     *
     * @return AbstractTreeListIterator
     */
    public function getSelfChilds(): AbstractTreeListIterator
    {
        if ($this->childs !== null) {
            return $this->childs;
        }
        return new TreeListEmptyIterator();
    }
}
