<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

use RS\Module\AbstractModel\TreeCookieList;
use RS\Module\AbstractModel\TreeList;
use RS\Orm\AbstractObject;

/**
 * Узел древовидного списка с orm объектом
 */
class TreeListOrmNode extends AbstractTreeListNode
{
    /** @var TreeList */
    protected $api;
    /** @var TreeListOrmPreLoader */
    protected $pre_loader;

    /**
     * TreeListOrmNode constructor.
     *
     * @param AbstractObject $object - orm объект узла
     * @param TreeList $api - api для получения дочерних узлов
     */
    public function __construct(AbstractObject $object, TreeList $api)
    {
        $this->setApi($api);
        parent::__construct($object);
    }

    /**
     * Возвращает является ли ветка дерева развёрнутой
     *
     * @return bool
     */
    public function isOpened()
    {
        if ($this->api instanceof TreeCookieList) {
            $object = $this->getObject();
            return in_array($object[$object->getPrimaryKeyProperty()], $this->getApi()->getOpenedElements());
        }
        return false;
    }

    /**
     * Возвращает итератор со списком дочерних элементов
     *
     * @return AbstractTreeListIterator
     */
    public function getSelfChilds()
    {
        $object = $this->getObject();
        $iterator = new TreeListOrmIterator($this->getApi(), $object[$this->getApi()->getIdField()]);
        if ($this->getPreLoader()) {
            $iterator->setPreLoader($this->getPreLoader());
        }
        return $iterator;
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
     * Возвращает объект предворительной загрузки
     *
     * @return TreeListOrmPreLoader|null
     */
    public function getPreLoader()
    {
        return $this->pre_loader;
    }

    /**
     * Устанавливает объект предворительной загрузки
     *
     * @param TreeListOrmPreLoader $pre_loader
     * @return void
     */
    public function setPreLoader(TreeListOrmPreLoader $pre_loader)
    {
        $this->pre_loader = $pre_loader;
    }

    /**
     * Возвращает идентификатор узла
     *
     * @return string|int
     */
    public function getID()
    {
        $object = $this->getObject();
        return $object[$this->getApi()->getIdField()];
    }

    /**
     * Возвращает имя узла
     *
     * @return string
     */
    public function getName()
    {
        $object = $this->getObject();
        return $object[$this->getApi()->getNameField()];
    }

    /**
     * Возвращает объект api
     *
     * @return TreeList
     */
    protected function getApi()
    {
        return $this->api;
    }

    /**
     * Устанавливает объект api
     *
     * @param TreeList $api - объект api
     */
    protected function setApi($api)
    {
        $this->api = $api;
    }
}
