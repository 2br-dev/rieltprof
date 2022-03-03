<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Catalog\Model\OrmType;

use Catalog\Model\Orm\OneClickItem;
use RS\Orm\Type\User;

/**
 * Поле - поиск ID покупки в 1 клик по строке
 */
class SelectOneClickItem extends User
{
    protected
        $cross_multisite = false; //Искать заказы на всех мультисайтах

    function __construct(array $options = null)
    {
        $this->view_attr = ['size' => 40, 'placeholder' => t('id, номер покупки в 1 клик, ФИО, телефон')];
        parent::__construct($options);
    }

    /**
     * Устанавливает, нужно ли выбирать заказы без учета мультисайтовости
     *
     * @param $bool
     */
    function setCrossMultisite($bool)
    {
        $this->cross_multisite = $bool;
    }

    /**
     * Возвращает, нужно ли выбирать заказы
     *
     * @return bool
     */
    function isCrossMultisite()
    {
        return $this->cross_multisite;
    }

    /**
     * Возвращает выбранный объект
     *
     * @return OneClickItem
     */
    function getSelectedObject()
    {
        $deal_id = ($this->get()>0) ? $this->get() : null;
        if ($deal_id>0) {
            if (!isset(self::$cache[$deal_id])) {
                $deal = new OneClickItem($deal_id);
                self::$cache[$deal_id] = $deal;
            }
            return self::$cache[$deal_id];
        }
        return new OneClickItem();
    }

    /**
     * Возвращает URL, который будет возвращать результат поиска
     *
     * @return string
     */
    function getRequestUrl()
    {
        return $this->request_url ?: \RS\Router\Manager::obj()->getAdminUrl('ajaxSearchOneClickItem', [
            'cross_multisite' => (int)$this->isCrossMultisite()
        ], 'catalog-tools');
    }

    /**
     * Возвращает наименование найденного объекта
     *
     * @return string
     */
    function getPublicTitle()
    {
        $one_click_item = $this->getSelectedObject();

        return t('Покупка в 1 клик №%num от %date', [
            'num' => $one_click_item['id'],
            'date' => date('d.m.Y', strtotime($one_click_item['dateof']))
        ]);
    }

    /**
     * Возвращает класс иконки zmdi
     *
     * @return string
     */
    function getIconClass()
    {
        return 'assignment';
    }
}