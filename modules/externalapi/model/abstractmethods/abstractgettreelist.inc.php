<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\AbstractMethods;

/**
* Абстрактный класс для загрузки списка объектов в древово
*/
abstract class AbstractGetTreeList extends AbstractFilteredList
{    
    /**
    * Возвращает список объектов
    * 
    * @param \RS\Module\AbstractModel\TreeList $dao
    * @param integer $parent_id id родительской категории
    * @return array
    */
    public function getResultList($dao, $parent_id)
    {
        return \ExternalApi\Model\Utils::extractOrmTreeList( $dao->getTreeList($parent_id) );
    }
    
   
    
    /**
    * Выполняет запрос на выборку объектов
    * 
    * @return array Возвращает список объектов и связанные с ним сведения.
    */
    protected function process($token, 
                               $parent_id = 0, 
                               $filter = [],
                               $sort = 'id desc')
    {                           
        $this->dao = $this->getDaoObject();
        $this->setFilter($this->dao, $filter);    
        $this->setOrder($this->dao, $sort);
        
        $response = [
            'response' => [
                $this->getObjectSectionName() => $this->getResultList($this->dao, $parent_id),
            ]
        ];

        return $response;
    }
}
