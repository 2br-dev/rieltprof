<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;

use Crm\Model\Orm\Telephony\CallHistory;
use Users\Model\Orm\User;

/**
 * Класс обеспечивает фильтрацию по номеру абонента в звонках
 */
class CallNumberClient extends \RS\Html\Filter\Type\User
{
    protected $search_type = 'custom';
    /**
     * Возвращает условие для выборки
     *
     * @return string
     */
    public function where_custom()
    {
        $user_id = $this->getValue();
        $user = new User($user_id);

        return "(call_flow = '".CallHistory::CALL_FLOW_IN."' AND caller_number = '".$this->escape($user['phone'])."')".
        "OR (call_flow = '".CallHistory::CALL_FLOW_OUT."' AND called_number = '".$this->escape($user['phone'])."')";
    }

    /**
     * Возвращает текстовое значение фильтра
     *
     * @return string
     */
    function getTextValue()
    {
        $user = new \Users\Model\Orm\User($this->getValue());
        return $user->getFio(). ($user['phone'] ? " ({$user['phone']})" : "");
    }
}