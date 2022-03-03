<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;

use Crm\Model\Orm\Deal;
use Crm\Model\OrmType\SelectDeal;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager;
use Shop\Model\Orm\Order;

/**
 * Связь объекта со сделками
 */
class LinkTypeDeal extends AbstractType
{
    public $linked_object_id;
    /**
     * @var Order
     */
    public $linked_object;

    /**
     * Возвращает имя закладки, характеризующей данную связь
     *
     * @return string
     */
    public function getTabName()
    {
        return t('Сделка');
    }

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    public function getTabForm()
    {
        $form = new FormObject(new PropertyIterator([
            'deal_id' => new SelectDeal([
                'description' => t('Поиск по сделкам'),
                'checker' => ['ChkEmpty', t('Сделка не выбрана')]
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
        return $tab_form['deal_id'];
    }


    /**
     * Инициализирует связь объекта с одним конкретным заказом
     * После данного метода можно вызывать методы визуализации
     *
     * @param $linked_object_id
     */
    public function init($linked_object_id)
    {
        $this->linked_object_id = $linked_object_id;
        $this->linked_object = new Deal($this->linked_object_id);
    }

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    public function getLinkText()
    {
        if ($this->linked_object_id < 0) {
            return t('Новая сделка (не сохраненная)');
        }

        if ($this->linked_object['id']) {
            return t('Сделка N%num от %date', [
                'num' => $this->linked_object['deal_num'],
                'date' => $this->linked_object['date_of_create']
            ]);
        } else {
            return t('Сделка не найдена (ID: %id)', [
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
        if ($this->linked_object['id'] > 0) {
            $url = Manager::obj()->getAdminUrl('edit', ['id' => $this->linked_object->id], 'crm-dealctrl');
            return $url;
        }
    }
}