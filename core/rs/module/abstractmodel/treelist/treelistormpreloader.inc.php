<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Module\AbstractModel\TreeList;

use RS\Module\AbstractModel\TreeList;

/**
 * Класс для предварительной загрузки orm-узлов
 */
class TreeListOrmPreLoader
{
    /** @var TreeList */
    protected $api;
    protected $loaded_data;

    /**
     * TreeListOrmPreLoader constructor.
     *
     * @param TreeList $api - объект api
     */
    public function __construct(TreeList $api)
    {
        $this->setApi($api);
        $this->initializeLoadedData();
    }

    /**
     * Возвращает детей указанного узла из запомненных данных, или false если нет данных для указанного узла
     *
     * @param $parent_id - id родительского узла
     * @return TreeListOrmNode[]|bool
     */
    public function getNodesByParentId($parent_id)
    {
        if ($this->hasNodesByParentId($parent_id)) {
            $result = [];
            $data = $this->getLoadedData();
            foreach ($data[$parent_id] as $item) {
                $api = $this->getApi();
                $orm = clone $api->getElement();
                $node = new TreeListOrmNode($orm->getFromArray($item, null, false, true), $this->getApi());
                $node->setPreLoader($this);
                if (isset($data[$item[$api->getIdField()]])) {
                    $childs_count = count($data[$item[$api->getIdField()]]);
                    $node->setChildsCount($childs_count);
                    if ($childs_count == 0) {
                        $node->setChilds(new TreeListEmptyIterator());
                    }
                }
                $result[] = $node;
            }
            return $result;
        }
        return false;
    }

    /**
     * Проверяет наличие данных о детях укаканного узла
     *
     * @param int $parent_id - id родительского узла
     * @return bool
     */
    public function hasNodesByParentId($parent_id)
    {
        $data = $this->getLoadedData();
        return isset($data[$parent_id]);
    }

    /**
     * Загружает данные и запоминает их
     *
     * @return void
     */
    protected function initializeLoadedData()
    {
        $api = $this->getApi();
        $list = $api->getListAsArray();
        $data = [];
        $id_field = $api->getIdField();
        $parent_field = $api->getParentField();
        foreach ($list as $item) {
            if (!isset($data[$item[$id_field]])) {
                $data[$item[$id_field]] = [];
            }
            $data[$item[$parent_field]][] = $item;
        }
        $this->setLoadedData($data);
    }

    /**
     * Возвращает запомненные данные
     *
     * @return array
     */
    public function getLoadedData()
    {
        return $this->loaded_data;
    }

    /**
     * Запоминает данные
     *
     * @param array $loaded_data
     */
    public function setLoadedData(array $loaded_data)
    {
        $this->loaded_data = $loaded_data;
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
     * @return void
     */
    protected function setApi($api)
    {
        $this->api = $api;
    }
}
