<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\OrmType;

use RS\Orm\Type\UserTemplate;

/**
 * Блок задач. Нужно размещать в отдельной вкладке ORM объекта
 */
class TaskBlock extends UserTemplate
{
    protected
        $link_type,
        $only_exists;

    /**
     * Конструктор класса
     * @param null|array $options - массив дополнительных параметров
     */
    function __construct($options = null)
    {
        $template = '%crm%/admin/ormtype/taskblock.tpl';
        parent::__construct($template, '', $options);
    }

    /**
     * Устанавливает тип связи, с которым нужно выбирать объекты
     *
     * @param string $link_type
     * @return void
     */
    function setLinkType($link_type)
    {
        $this->link_type = $link_type;
    }

    /**
     * Возвращает установленный тип связи
     *
     * @return string
     */
    function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * Устанавливает, нужно ли запрещать возможность работы с блоком, если связываемый объект еще не записан в базу (не создан)
     *
     * @param $bool
     */
    function setOnlyExists($bool)
    {
        $this->only_exists = $bool;
    }

    /**
     * Возвращает true, если блок не должен работать, пока связываемый объект не создан
     *
     * @return bool
     */
    function isOnlyExists()
    {
        return $this->only_exists;
    }
}