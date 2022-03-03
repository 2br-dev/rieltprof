<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\FilterType;

use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\Telephony\Manager;
use Users\Model\Orm\User;

/**
 * Класс обеспечивает фильтрацию по номеру абонента в звонках
 */
class CallNumberAdmin extends \RS\Html\Filter\Type\User
{
    protected $search_type = 'custom';
    public $tpl = '%crm%/admin/filtertype/call_number_admin.tpl';
    protected $list;

    function __construct($key, $title, $options = [])
    {
        $this->setList(Manager::getProvidersTitles());
        parent::__construct($key, $title, $options);
    }

    /**
     * Возвращает список провайдеров
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Устанавливает список провайдеров
     *
     * @param $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

    /**
     * Возвращает условие для выборки
     *
     * @return string
     */
    public function where_custom()
    {
        $data = $this->getValue();
        if (empty($data['user_id'])) return '';

        $provider = Manager::getProviderById($data['provider']);
        $extension_id = $provider->getExtensionIdByUserId($data['user_id']);

        return "provider = '".$provider->getId()."' AND (call_flow = '".CallHistory::CALL_FLOW_IN."' AND called_number = '".$this->escape($extension_id)."')".
            "OR (call_flow = '".CallHistory::CALL_FLOW_OUT."' AND caller_number = '".$this->escape($extension_id)."')";
    }

    /**
     * Возвращает текстовое значение фильтра
     *
     * @return string
     */
    function getTextValue()
    {
        $value = $this->getValue();
        $provider = Manager::getProviderById($value['provider']);
        $user = new \Users\Model\Orm\User($value['user_id']);
        return $provider->getTitle().' - '.$user->getFio();
    }

    /**
     * Возвращает ФИО пользователя
     *
     * @return string
     */
    function getUserFio()
    {
        $value = $this->getValue();
        $user = new \Users\Model\Orm\User($value['user_id']);
        return $user->getFio();
    }

    /**
     * Возвращает null, если филтр не установлен, иначе значение фильтра
     */
    function getNonEmptyValue()
    {
        if (!empty($this->value['user_id']) || !$this->emptynull) {
            return $this->getValue();
        }
        return null;
    }

    /**
     * Возвращает ключ-значение, поля в виде ассоциативного массива, если есть значение, иначе пустой массив
     * @return array
     */
    function getKeyVal()
    {
        if (empty($this->value['user_id']) && $this->emptynull) return [];
        return [$this->key => $this->getValue()];
    }

}