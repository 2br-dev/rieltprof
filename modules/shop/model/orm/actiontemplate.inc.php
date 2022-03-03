<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\Orm;
use RS\Orm\OrmObject;
use RS\Orm\Type;
use Shop\Model\Notice\RunActionToUser;

/**
 * ORM объект одного шаблона действия курьера по отношению к заказу.
 * Например, курьер не дозвонился до клиента
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $title Название действия (коротко)
 * @property string $client_sms_message Текст SMS сообщения, направляемого клиенту
 * @property string $client_email_message Текст Email сообщения, направляемого клиенту
 * @property integer $public Включен
 * --\--
 */
class ActionTemplate extends OrmObject
{
    protected static
        $table = 'order_action_template';

    function _init()
    {
        parent::_init()->append([
            'site_id' => new Type\CurrentSite(),
            'title' => new Type\Varchar([
                'description' => t('Название действия (коротко)'),
                'hint' => t('Например, Клиент не отвечает на звонки')
            ]),
            'client_sms_message' => new Type\Text([
                'description' => t('Текст SMS сообщения, направляемого клиенту'),
                'hint' => t('Для работы данной функции, необходимо, чтобы у вас был настроен транспортный модуль для СМС')
            ]),
            'client_email_message' => new Type\Richtext([
                'description' => t('Текст Email сообщения, направляемого клиенту')
            ]),
            'public' => new Type\Integer([
                'description' => t('Включен'),
                'checkboxView' => [1,0]
            ])
        ]);
    }

    /**
     * Выполняет текущий шаблон для указанного заказа
     *
     * @param $order
     */
    public function run($order)
    {
        $notice = new RunActionToUser();
        $notice->init($order, $this);
        \Alerts\Model\Manager::send($notice);

        return true;
    }
}