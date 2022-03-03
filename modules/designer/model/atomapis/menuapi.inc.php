<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Designer\Model\AtomApis;

use Catalog\Model\DirApi;
use RS\Exception as RSException;
use RS\Module\AbstractModel\BaseModel;

/**
 * Класс API для компонента меню
 */
class MenuApi extends BaseModel
{
    /**
     * Возвращает информацию по пункту меню
     *
     * @param integer $showAll - показывать все категории или только те у которых есть под категории
     * @param $child
     */
    private function getMenuItemInfo($showAll, $child)
    {
        $arr['id']     = $child['fields']['id'];
        $arr['title']  = $child['fields']['title'];
        $arr['public'] = $child['fields']['public'];
        $arr['link']   = $child['fields']->getHref();
        $arr['childs'] = $this-> getTreeLeavesAsArray($showAll, $child->getChilds());
        return $arr;
    }

    /**
     * Возвращает пункты меню в виде массива
     *
     * @param integer $showAll - показывать все категории или только те у которых есть под категории
     * @param \RS\Module\AbstractModel\TreeList\TreeListOrmIterator $childs - потомки дерева
     * @return array
     */
    private function getTreeLeavesAsArray($showAll, $childs)
    {
        $arr = [];
        foreach ($childs as $child){
            if (!$showAll){
                if ($child->getChildsCount() > 0){
                    $arr[$child['fields']['id']] = $this->getMenuItemInfo($showAll, $child);
                }
            }else{
                if ($child['fields']['public']){
                    $arr[$child['fields']['id']] = $this->getMenuItemInfo($showAll, $child);
                }
            }
        }
        return $arr;
    }

    /**
     * Возвращает дерево меню для компонента в виде массива
     *
     * @param bool $showAll - показывать все категории или только те у которых есть под категории
     * @param integer $root - корнево каталог
     *
     * @return array
     *
     * @throws \RS\Exception
     */
    function getTreeForMenus($showAll, $root = 0)
    {
        $menuApi = new \Menu\Model\Api();
        $menuApi->setFilter('menutype', 'user');
        $menusTree = $menuApi->getTreeList($root);
        $menus = $this->getTreeLeavesAsArray($showAll, $menusTree->getItems());

        if (!$showAll){
            $root = [[
                'id'    => 0,
                'title' => t('- Корень каталога -'),
            ]];
            $arr = array_merge($root, $menus);
            return $arr;
        }
        return $menus;
    }

    /**
     * Возвращает дерево категорий для компонента в виде массива для определённого корневого каталога
     *
     * @param integer $root - корнево каталог
     *
     * @return array
     * @throws \RS\Exception
     */
    function getSimpleTreeForCategory($root = 0)
    {
        $arr  = [];

        $list = DirApi::getInstance()
                ->setFilter('public', 1)
                ->getTreeList($root);
        if (!$root){ //Если корень, то добавим
            $arr[] = [
                'id'    => 0,
                'title' => t('- Корень каталога -'),
                'childscount' => 0,
                'childs' => []
            ];
        }
        foreach ($list as $dir){
            if ($dir->getChildsCount()){
                $have_sub_childs = false;
                $sub_childs = $dir->getChilds();
                foreach ($sub_childs as $sub_child){
                    if ($sub_child->getChildsCount()){
                        $have_sub_childs = true;
                    }
                }

                $arr[] = [
                    'id'    => $dir['fields']['id'],
                    'title' => $dir['fields']['name'],
                    'childscount' => $have_sub_childs ? $dir->getChildsCount() : 0,
                    'childs' => []
                ];
            }
        }

        return $arr;
    }

    /**
     * Возвращает дерево категорий для компонента в виде массива для определённого корневого каталога
     *
     * @param integer $root - корнево каталог
     *
     * @return array
     * @throws \RS\Exception
     */
    function getTreeForCategory($root = 0)
    {
        $arr  = [];
        $list = DirApi::getInstance()
                ->setFilter('public', 1)
                ->getTreeList($root);

        $firstWithChildsFound = false;
        foreach ($list as $dir){
            $firstWithChilds = false;
            $childs = ($dir['fields']['level'] < 3) ? $this->getTreeForCategory($dir['fields']['id']) : [];
            if (!empty($childs) && !$firstWithChildsFound){
                $firstWithChilds = true;
                $firstWithChildsFound = true;
            }
            $arr[] = [
                'id'     => $dir['fields']['id'],
                'title'  => $dir['fields']['name'],
                'public' => $dir['fields']['public'],
                'link'   => $dir['fields']->getUrl(),
                'firstWithChilds' => $firstWithChilds,
                'childs' => $childs
            ];
        }

        return $arr;
    }
}