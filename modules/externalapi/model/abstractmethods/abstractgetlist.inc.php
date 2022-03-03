<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace ExternalApi\Model\AbstractMethods;

/**
* Абстрактный класс для загрузки списка объектов
*/
abstract class AbstractGetList extends AbstractFilteredList
{
    /**
    * Возвращает список объектов
    * 
    * @param \RS\Module\AbstractModel\EntityList $dao
    * @param integer $page
    * @param integer $pageSize
    * @return array
    */
    public function getResultList($dao, $page, $pageSize)
    {
        return \ExternalApi\Model\Utils::extractOrmList( $dao->getList($page, $pageSize) );
    }

    /**
     * Выполняет запрос на выборку объектов
     *
     * @param string $token - авторизовационный токен
     * @param array $filter - массив из фильтров для применения
     * @param string $sort - сортировка
     * @param string $page - текущий номер страницы
     * @param string $pageSize - размер элементов в порции
     *
     * @return array Возвращает список объектов и связанные с ним сведения.
     * @throws \ExternalApi\Model\Exception
     */
    protected function process($token, 
                               $filter = [],
                               $sort = 'id desc', 
                               $page = "1", 
                               $pageSize = "20")
    {
        $this->dao = $this->getDaoObject();
        $this->setFilter($this->dao, $filter);
        $this->setOrder($this->dao, $sort);
        
        $response = [
            'response' => [
                'summary' => [
                    'page' => $page,
                    'pageSize' => $pageSize,
                    'total' => $this->getResultCount($this->dao),
                ],
                $this->getObjectSectionName() => $this->getResultList($this->dao, $page, $pageSize),
            ]
        ];

        return $response;
    }
}
