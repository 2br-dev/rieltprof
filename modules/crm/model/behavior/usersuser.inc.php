<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Behavior;

use Catalog\Model\OneClickItemApi;
use Crm\Model\DealApi;
use Crm\Model\InteractionApi;
use Crm\Model\Links\Type\LinkTypeCall;
use Crm\Model\Links\Type\LinkTypeUser;
use Crm\Model\Orm\Deal;
use Crm\Model\Orm\Interaction;
use Crm\Model\Orm\Task;
use Crm\Model\TaskApi;
use RS\Behavior\BehaviorAbstract;
use Shop\Model\OrderApi;
use Shop\Model\Orm\Order;
use Shop\Model\Orm\Reservation;
use Shop\Model\ReservationApi;
use Support\Model\Orm\Topic;
use Support\Model\TopicApi;

/**
 * Расширяет объект пользователя методами, необходимыми для CRM
 */
class UsersUser extends BehaviorAbstract
{
    /**
     * Возвращает последние $limit заказов.
     * Если $limit = false, то возвращает общее количество заказов
     *
     * @param int $limit
     * @return integer|Order[]
     */
    public function getLastOrders($limit = 5)
    {
        $order_api = new OrderApi();
        $order_api->setFilter('user_id', $this->owner->id);
        if ($limit === false) {
            return $order_api->getListCount();
        }

        return $order_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit взаимодействий
     *
     * @param int $limit
     *
     * @return integer|Interaction[]
     */
    public function getLastInteractions($limit = 5)
    {
        $interaction_api = new InteractionApi();
        if ($this->owner->id > 0) {
            $interaction_api->addFilterByLink(LinkTypeUser::getId(), $this->owner->id);
        }
        $interaction_api->addFilterByLink(LinkTypeCall::getId(), $this->owner->call_history->id);

        if ($limit === false) {
            $q = $interaction_api->queryObj();
            $q->select = 'COUNT(DISTINCT A.id) as cnt';

            return $q->exec()->getOneField('cnt', 0);
        }

        $interaction_api->setGroup('A.id');
        return $interaction_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit сделок
     *
     * @param int $limit
     * @return integer|Deal[]
     */
    public function getLastDeals($limit = 5)
    {
        $deal_api = new DealApi();

        if ($this->owner->id > 0) {
            $deal_api->setFilter('client_type', Deal::CLIENT_TYPE_USER);
            $deal_api->setFilter('client_id', $this->owner->id);
        } else {
            $deal_api->setFilter('client_type', Deal::CLIENT_TYPE_GUEST);
            $deal_api->setFilter('client_name', $this->owner->phone, '%like%');
        }

        if ($limit === false) {
            return $deal_api->getListCount();
        }
        return $deal_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit задач
     *
     * @param int $limit
     * @return integer|Task[]
     */
    public function getLastTasks($limit = 5)
    {
        $task_api = new TaskApi();
        if ($this->owner->id > 0) {
            $task_api->addFilterByLink(LinkTypeUser::getId(), $this->owner->id);
        }
        $task_api->addFilterByLink(LinkTypeCall::getId(), $this->owner->call_history->id);

        if ($limit === false) {
            return $task_api->getListCount();
        }

        $task_api->setGroup('A.id');
        return $task_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit покупок в 1 клик
     *
     * @param int $limit
     * @return array
     * @throws \RS\Orm\Exception
     */
    public function getLastOneClick($limit = 5)
    {
        $one_click_api = new OneClickItemApi();
        $one_click_api->setFilter('user_phone', $this->owner->phone);

        if ($limit === false) {
            return $one_click_api->getListCount();
        }

        return $one_click_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit предзаказов
     *
     * @param int $limit
     * @return Reservation[]
     * @throws \RS\Orm\Exception
     */
    public function getLastReservation($limit = 5)
    {
        $reservation_api = new ReservationApi();
        $reservation_api->setFilter('phone', $this->owner->phone);

        if ($limit === false) {
            return $reservation_api->getListCount();
        }

        return $reservation_api->getList(1, $limit, 'id desc');
    }

    /**
     * Возвращает последние $limit обращений в поддержку
     *
     * @return Topic[]
     * @throws \RS\Orm\Exception
     */
    public function getLastSupport($limit = 5)
    {
        $support_api = new TopicApi();
        $support_api->setFilter('user_id', $this->owner->id);

        if ($limit === false) {
            return $support_api->getListCount();
        }

        return $support_api->getList(1, $limit, 'updated desc');
    }
}