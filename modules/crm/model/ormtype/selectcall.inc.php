<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\OrmType;

use Crm\Model\Orm\Telephony\CallHistory;
use RS\Orm\Type\User;

class SelectCall extends User
{
    function __construct(array $options = null)
    {
        parent::__construct($options);
        $this->setAttr(['placeholder' => t('Номер телефона, ИД звонка')]);
    }

    /**
     * @return CallHistory
     */
    function getSelectedObject()
    {
        $call_history_id = ($this->get()>0) ? $this->get() : null;
        if ($call_history_id>0) {
            if (!isset(self::$cache[$call_history_id])) {
                $deal = new CallHistory($call_history_id);
                self::$cache[$call_history_id] = $deal;
            }
            return self::$cache[$call_history_id];
        }
        return new CallHistory();
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchCall', null, 'crm-ajaxlist');
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     */
    function getPublicTitle()
    {
        return $this->getSelectedObject()->getPublicTitle();
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    function getIconClass()
    {
        return 'assignment-check';
    }
}