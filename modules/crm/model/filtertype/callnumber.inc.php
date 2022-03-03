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
class CallNumber extends \RS\Html\Filter\Type\Text
{
    protected $search_type = 'custom';
    /**
     * Возвращает условие для выборки
     *
     * @return string
     */
    public function where_custom()
    {
        $phone = $this->getValue();

        return "(call_flow = '".CallHistory::CALL_FLOW_IN."' AND caller_number like '%".$this->escape($phone)."%')".
        "OR (call_flow = '".CallHistory::CALL_FLOW_OUT."' AND called_number like '%".$this->escape($phone)."%')";
    }
}