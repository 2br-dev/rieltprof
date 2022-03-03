<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;
use Crm\Config\ModuleRights;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use Users\Model\Orm\User;

/**
* Класс для организации выборок ORM объекта.
*/
class DealApi extends AbstractLinkedApi
{
    protected
        $creator_user_id_field = 'manager_id';

    function __construct()
    {
        parent::__construct(new Orm\Deal(), [
            'sortField' => 'board_sortn'
        ]);
    }

    /**
     * Ищет сделку по различным полям
     *
     * @param $term
     * @param $fields
     * @param $limit
     */
    public function search($term, $fields, $limit)
    {
        $this->resetQueryObject();
        $q = $this->queryObj();
        $q->select = 'A.*';

        $q->openWGroup();
        if (in_array('user', $fields)) {
            $q->leftjoin(new User(), 'U.id = A.client_id', 'U');
            $q->where("CONCAT(`U`.`surname`, ' ', `U`.`name`,' ', `U`.`midname`) like '%#term%'", [
                'term' => $term
            ]);
        }

        foreach($fields as $field) {
            if ($field == 'user') continue;
            $this->setFilter($field, $term, '%like%', 'OR');
        }

        $q->closeWGroup();

        return $this->getList(1, $limit);
    }

    /**
     * Устанавливает фильтры, которые соответствуют правам текущего пользователя
     */
    function initRightsFilters()
    {
        //Если у пользователя нет прав на просмотр чужих объектов, то не отображаем их.
        $user = Auth::getCurrentUser();

        if (!Rights::hasRight($this, ModuleRights::DEAL_OTHER_READ)) {
            $filters = [
                //Отображаем только те объекты, которые мы создали
                $this->creator_user_id_field => $user['id']
            ];

            $this->setFilter($filters);
        }
    }


    /**
     * Добавляет фильтр, который исключает архивные сделки
     *
     * @return void
     */
    public function excludeArchivedItems()
    {
        $this->setFilter('is_archived', 0);
    }
}