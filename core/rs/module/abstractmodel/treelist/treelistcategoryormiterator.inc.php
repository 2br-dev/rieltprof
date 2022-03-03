<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

use RS\Module\AbstractModel\EntityList;
use RS\Orm\Request as OrmRequest;

/**
 * Итератор категорий списка с orm-узлами
 */
class TreeListCategoryOrmIterator extends AbstractTreeListIterator
{
    /** @var EntityList */
    protected $api;
    /** @var EntityList */
    protected $category_api;
    /** @var int */
    protected $parent_id;
    /** @var TreeListOrmPreLoader */
    protected $pre_loader;

    /**
     * TreeListIterator constructor.
     *
     * @param EntityList $api - api для выборки объектов
     * @param EntityList $category_api - api для выборки объектов категорий
     * @param int $parent_id - id родительского узла
     */
    public function __construct(EntityList $api, EntityList $category_api, $parent_id = null)
    {
        $this->setApi($api);
        $this->setCategoryApi($category_api);
        $this->setParentId($parent_id);
    }

    /**
     * Возвращает список дочерних элементов
     *
     * @return TreeListOrmNode[]
     */
    protected function getSelfItems()
    {
        if ($this->getPreLoader() && $this->getPreLoader()->hasNodesByParentId($this->getParentId())) {
            $items = $this->getPreLoader()->getNodesByParentId($this->getParentId());
        } else {
            $items = [];
            if ($this->getParentId() === null) {
                $api = $this->getCategoryApi();
            } else {

            }


            $list = $this->getClonedQueryObj()->where([$api->getParentField() => $this->getParentId()])->objects(null, $api->getIdField());


            foreach ($list as $id => $item) {
                $node = new TreeListOrmNode($item, $api);
                $node->setChildsCount($childs_count);
                if ($childs_count == 0) {
                    $node->setChilds(new TreeListEmptyIterator());
                }
                $items[] = $node;
            }
        }
        return $items;
    }

    /**
     * Возвращает копию api для сохранения установленных фильтров
     *
     * @return OrmRequest
     */
    protected function getClonedQueryObj()
    {
        return clone $this->getApi()->queryObj();
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
     * Возвращает объект api
     *
     * @return EntityList
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * Устанавливает объект api
     *
     * @param EntityList $api - объект api
     * @return void
     */
    protected function setApi($api)
    {
        $this->api = $api;
    }

    /**
     * Возвращает объект api категорий
     *
     * @return EntityList
     */
    public function getCategoryApi()
    {
        return $this->category_api;
    }

    /**
     * Устанавливает объект api
     *
     * @param EntityList $api - объект api категорий
     * @return void
     */
    protected function setCategoryApi($api)
    {
        $this->category_api = $api;
    }

    /**
     * Возвращает id родительского узла
     *
     * @return int
     */
    protected function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Устанавливает id родительского узла
     *
     * @param int $parent_id - id родительского узла
     * @return void
     */
    protected function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }
}
