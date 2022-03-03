<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model;

use Crm\Config\ModuleRights;
use Crm\Model\Links\Type\LinkTypeCall;
use Crm\Model\Links\Type\LinkTypeUser;
use Crm\Model\Orm\Deal;
use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Manager;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Config\Loader;
use RS\Db\Adapter;
use RS\Module\AbstractModel\EntityList;

/**
 * Класс обеспечивает выборки объектов из базы
 */
class CallHistoryApi extends EntityList
{
    function __construct()
    {
        parent::__construct(new Orm\Telephony\CallHistory(), [
            'loadOnDelete' => true
        ]);
    }

    /**
     * Устанавливает фильтры, которые соответствуют правам текущего пользователя
     *
     * @throws \RS\Exception
     */
    function initRightsFilters()
    {
        //Если у пользователя нет прав на просмотр чужих объектов, то не отображаем их.
        $user = Auth::getCurrentUser();
        if (!Rights::hasRight($this, ModuleRights::CALL_HISTORY_OTHER_READ))
        {

            $where = [];
            foreach(Manager::getProviders() as $provider) {
                $extension_id = Adapter::escape($provider->getExtensionIdByUserId($user['id']));
                $provider = Adapter::escape($provider->getId());

                $where[] = "(provider = '{$provider}' AND (caller_number = '{$extension_id}' OR called_number = '{$extension_id}'))";
            }

            if ($where) {
                $this->queryObj()->where('(' . implode(' OR ', $where) . ')');
            } else {
                $this->queryObj()->where(0); //Запрещаем вывод данных
            }
        }
    }

    /**
     * Возвращает данные для инициализации взаимодействия по звонку
     *
     * @param $call_history_id
     * @return array
     * @throws \RS\Exception
     */
    public static function getDataForInteraction($call_history_id)
    {
        $call_data = [];
        $call_api = new self();
        if ($call = $call_api->getOneItem($call_history_id)) {
            $call_data['links'] = [
                LinkTypeCall::getId() => [$call['id']]
            ];

            $client = $call->getOtherUser();
            if ($client['id'] > 0) {
                $call_data['links'][LinkTypeUser::getId()] = [$client['id']];
            }

            if ($call['call_flow'] == CallHistory::CALL_FLOW_IN) {
                $call_data['title'] = t('Входящий звонок от %number', [
                    'number' => $call['caller_number']
                ]);
            } else {
                $call_data['title'] = t('Исходящий звонок от %number', [
                    'number' => $call['caller_number']
                ]);
            }
        }

        return $call_data;
    }

    /**
     * Возвращает данные для инициализации сделки
     *
     * @param $call_history_id
     * @return array
     */
    public static function getDataForDeal($call_history_id)
    {
        $call_data = [];
        $call_api = new self();
        if ($call = $call_api->getOneItem($call_history_id)) {
            $client = $call->getOtherUser();
            if ($client['id'] > 0) {
                $call_data['client_type'] = Deal::CLIENT_TYPE_USER;
                $call_data['client_id'] = $client['id'];
            } else {
                $call_data['client_type'] = Deal::CLIENT_TYPE_GUEST;
                $call_data['client_name'] = t('Клиент с номером %number', [
                    'number' => $client['phone']
                ]);
            }
        }

        return $call_data;
    }

    /**
     * Возвращает данные для инициализации задачи
     * @param $call_history_id
     * @return array
     * @throws \RS\Exception
     */
    public static function getDataForTask($call_history_id)
    {
        $call_data = [];
        $call_api = new self();
        if ($call = $call_api->getOneItem($call_history_id)) {
            $call_data['links'] = [
                LinkTypeCall::getId() => [$call['id']]
            ];

            $client = $call->getOtherUser();
            if ($client['id'] > 0) {
                $call_data['links'][LinkTypeUser::getId()] = [$client['id']];
            }
        }

        return $call_data;
    }

}