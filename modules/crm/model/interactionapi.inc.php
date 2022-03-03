<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;
use Crm\Config\ModuleRights;
use Crm\Model\Orm\Link;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Orm\Request;

/**
 * Класс для организации выборок ORM объекта.
 */
class InteractionApi extends AbstractLinkedApi
{
    function __construct()
    {
        parent::__construct(new Orm\Interaction());
    }

    /**
     * Устанавливает фильтры, которые соответствуют правам текущего пользователя
     */
    function initRightsFilters()
    {
        //Если у пользователя нет прав на просмотр чужих объектов, то не отображаем их.
        $user = Auth::getCurrentUser();

        if (!Rights::hasRight($this, ModuleRights::INTERACTION_OTHER_READ)) {
            $filters = [
                //Отображаем только те объекты, которые мы создали
                $this->creator_user_id_field => $user['id']
            ];

            $this->setFilter($filters);
        }

    }
}