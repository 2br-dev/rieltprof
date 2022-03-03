<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Telephony\Provider;

use Crm\Model\Orm\Telephony\CallHistory;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type;

/**
 * Класс описывает объект, который содержит сведения
 * для окна тестирования телефонии
 */
abstract class AbstractProviderTest
{
    const CALL_FLOW_IN = 'in';
    const CALL_FLOW_OUT = 'out';
    const CALL_EVENT_TYPE_DIAL = 'dial';
    const CALL_EVENT_TYPE_ANSWER = 'answer';
    const CALL_EVENT_TYPE_HANGUP = 'hangup';

    private $provider;
    protected $last_event_result = '';
    protected $last_event_error = '';

    function __construct(AbstractProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Возвращает объект провайдера телефонии
     *
     * @return AbstractProvider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Возвращает объект, описывающий форму запроса данных у пользователя для тестирования
     *
     * @return null | FormObject
     */
    public function getFormObject()
    {
        $form_object = new FormObject(new PropertyIterator([
            'call_flow' => new Type\Varchar([
                'description' => t('Направление'),
                'listFromArray' => [[
                    self::CALL_FLOW_IN => t('Входящий звонок'),
                    self::CALL_FLOW_OUT => t('Исходящий звонок'),
                ]]
            ]),
            'call_event_type' => new Type\Varchar([
                'description' => t('Действие'),
                'listFromArray' => [[
                    self::CALL_EVENT_TYPE_DIAL => t('Звонок'),
                    self::CALL_EVENT_TYPE_ANSWER => t('Ответ на звонок'),
                    self::CALL_EVENT_TYPE_HANGUP => t('Завершение вызова'),
                ]]
            ]),
            'called_id' => new Type\Varchar([
                'description' => t('Добавочный номер абонента')
            ]),
            'caller_number' => new Type\Varchar([
                'description' => t('Номер звонящего'),
                'attr' => [[
                    'placeholder' => t('Например, +7(XXX)xxx-xx-xx')
                ]]
            ]),
            'show_request' => new Type\Integer([
                'description' => t('Показать запрос в уведомлении'),
                'checkboxView' => [1,0]
            ])
        ]));

        return $form_object;
    }

    /**
     * Возвращает HTML форму данного типа оплаты, для ввода дополнительных параметров
     *
     * @return string
     */
    function getFormHtml()
    {
        if ($params = $this->getFormObject()) {
            $params->getPropertyIterator()->arrayWrap('provider_fields');
            $params->setFormTemplate(strtolower(str_replace('\\', '_', get_class($this))));
            $module = \RS\Module\Item::nameByObject($this);
            $tpl_folder = \Setup::$PATH.\Setup::$MODULE_FOLDER.'/'.$module.\Setup::$MODULE_TPL_FOLDER;
            return $params->getForm(['provider_test' => $this], null, false, null, '%system%/coreobject/tr_form.tpl', $tpl_folder);
        }
    }

    /**
     * Обрабатывает запрос на тестирование
     *
     * @param array $data
     */
    abstract public function onTest(array $data);

    /**
     * Возвращает успешный результат последнего вызова onEvent
     *
     * @return string
     */
    public function getEventLastResult()
    {
        return $this->last_event_result;
    }

    /**
     * Возвращает текст ошибки последнего вызова onEvent
     *
     * @return string
     */
    public function getEventLastError()
    {
        return $this->last_event_error;
    }
}