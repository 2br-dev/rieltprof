<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;

use Crm\Model\CallHistoryApi;
use Crm\Model\Orm\Telephony\CallHistory;
use Crm\Model\OrmType\SelectCall;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager;
use Shop\Model\Orm\Order;

/**
 * Связь объекта с заказом
 */
class LinkTypeCall extends AbstractType
{
    public $linked_object_id;
    /**
     * @var Order
     */
    public $linked_object;

    protected $last_objects_template = '%crm%/admin/links/lastobjects/call.tpl';

    /**
     * Возвращает имя закладки, характеризующей данную связь
     *
     * @return string
     */
    public function getTabName()
    {
        return t('Звонок');
    }

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    public function getTabForm()
    {
        $form = new FormObject(new PropertyIterator([
            'call_history_id' => new SelectCall([
                'description' => t('Поиск по звонкам'),
                'checker' => ['ChkEmpty', t('Звонок не выбран')],
            ])
        ]));

        $form->setFormTemplate($this->getId());

        return $form;
    }


    /**
     * Возвращает ID связываемого объекта, опираясь на данные заполненного объекта формы
     *
     * @param FormObject $tab_form
     * @return integer
     */
    public function getLinkIdByTabFormObject($tab_form)
    {
        return $tab_form['call_history_id'];
    }

    /**
     * Инициализирует связь объекта с одним конкретным заказом
     * После данного метода можно вызывать методы визуализации
     *
     * @param $source_object
     * @param $linked_object_id
     */
    public function init($linked_object_id)
    {
        $this->linked_object_id = $linked_object_id;
        $this->linked_object = new CallHistory($this->linked_object_id);
    }

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    public function getLinkText()
    {
        if ($this->linked_object['id']) {
            return $this->linked_object->getPublicTitle();
        } else {
            return t('Звонок не найден (ID: %id)', [
                'id' => $this->linked_object_id
            ]);
        }
    }

    /**
     * Возвращает ссылку, которую нужно установить к тексту, при визуализации связи
     *
     * @return mixed
     */
    public function getLinkUrl()
    {
        if ($this->linked_object['id']) {
            $url = Manager::obj()->getAdminUrl('edit', ['id' => $this->linked_object->id], 'crm-callhistoryctrl');
            return $url;
        }
    }

    /**
     * Возвращает последние $limit объектов, с которыми возможно установить связь
     *
     * @param integer $limit
     * @return []
     */
    public function getLastObjects($limit = null)
    {
        if (!$limit) {
            $limit = 10;
        }
        $api = new CallHistoryApi();
        return $api->getList(1, $limit, 'id DESC');
    }
}