<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Article\Model;

class Api extends \RS\Module\AbstractModel\EntityList
{    
    function __construct()
    {
        parent::__construct(new \Article\Model\Orm\Article,
            [
                'aliasField' => 'alias',
                'nameField' => 'title', 
                'multisite' => true,
                'defaultOrder' => 'dateof DESC'
            ]);
    }
    
    /**
    * Возвращает ленту статей в категории. Используется для новостей
    */
    public function getLineByCat($cat, $limit, $order = '')
    {
        $q = new \RS\Orm\Request();
        $q->select("*")
            ->from( $this->obj_instance )
            ->where( ['parent' => $cat])
            ->limit(0, $limit);
        
        if (!empty($order)) $q->orderby($order);
        
        return $q->objects();
    }
    
    public function getByAlias($alias, $clearFilterBefore = false)
    {
    	if ($clearFilterBefore) $this->clearFilter();
        
        $this->setFilter('alias', $alias);
        $list = $this->getList(0,0,'dateof DESC');
        if (count($list)>0) {
            return $list[0];
        }
    }

}

