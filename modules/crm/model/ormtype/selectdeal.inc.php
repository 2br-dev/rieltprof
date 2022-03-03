<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\OrmType;

use Crm\Model\Orm\Deal;
use RS\Orm\Type\User;

class SelectDeal extends User
{
    function __construct(array $options = null)
    {
        parent::__construct($options);
        $this->setAttr(['placeholder' => t('id, номер сделки, ФИО')]);
    }

    /**
     * @return Deal
     */
    function getSelectedObject()
    {
        $deal_id = ($this->get()>0) ? $this->get() : null;
        if ($deal_id>0) {
            if (!isset(self::$cache[$deal_id])) {
                $deal = new Deal($deal_id);
                self::$cache[$deal_id] = $deal;
            }
            return self::$cache[$deal_id];
        }
        return new Deal();
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchDeal', null, 'crm-ajaxlist');
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