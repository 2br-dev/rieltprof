<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\OrmType;

use RS\Config\UserFieldsManager;
use RS\Orm\Type\ArrayList;
use RS\Orm\Type\UserTemplate;

/**
 * Поле, отображающее дополнительные поля, созданные пользователем в настройках модуля
 */
class CustomFields extends UserTemplate
{
    protected $field_manager;

    function __construct($options = null)
    {
        $template = '%crm%/admin/ormtype/customfields.tpl';
        parent::__construct($template, '', $options);
    }

    /**
     * Возвращает объект менеджера дополнительных полей
     *
     * @return UserFieldsManager
     */
    function getFieldsManager()
    {
        return $this->field_manager;
    }

    /**
     * Устанавливает объект менеджера дополнительных полей
     *
     * @param UserFieldsManager $field_manager
     * @return void
     */
    function setFieldsManager(UserFieldsManager $field_manager)
    {
        $this->field_manager = $field_manager;
    }
}