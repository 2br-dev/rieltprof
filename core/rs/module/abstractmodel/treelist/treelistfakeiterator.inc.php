<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

/**
 * Несуществующий итератор древовидного списка, заполняется вручную
 */
class TreeListFakeIterator extends AbstractTreeListIterator
{
    /** @var AbstractTreeListNode[] */
    protected $self_items = [];

    public function addItem(AbstractTreeListNode $node)
    {
        $this->self_items[] = $node;
    }

    /**
     * Возвращает список узлов, составляющих путь к указанному элементу от корня
     *
     * @param string|int $node_id - идентификатор целевого узла
     * @return AbstractTreeListNode[]
     */
    public function getPathFromRoot($node_id): array
    {
        $path = [];
        foreach ($this->getSelfItems() as $node) {
            if ($node->getID() == $node_id) {
                $path = [$node];
                break;
            } elseif ($node->getChildsCount()) {
                $find_path = $node->getChilds()->getPathFromRoot($node_id);
                if (!empty($find_path)) {
                    $path = array_merge([$node], $find_path);
                    break;
                }
            }
        }
        return $path;
    }

    /**
     * Возвращает список дочерних элементов
     *
     * @return AbstractTreeListNode[]
     */
    protected function getSelfItems()
    {
        return $this->self_items;
    }
}
