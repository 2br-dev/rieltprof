<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;

use Catalog\Model\Orm\OneClickItem;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Router\Manager;
use Shop\Model\OrmType\SelectReservation;

/**
 * Связь объекта с заказом
 */
class LinkTypeReservation extends AbstractType
{
    public $linked_object_id;
    /**
     * @var OneClickItem
     */
    public $linked_object;

    /**
     * Возвращает имя закладки, характеризующей данную связь
     *
     * @return string
     */
    public function getTabName()
    {
        return t('Предзаказ');
    }

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    public function getTabForm()
    {
        $form = new FormObject(new PropertyIterator([
            'reservation_id' => new SelectReservation([
                'description' => t('Поиск по предзаказам'),
                'crossMultisite' => true,
                'checker' => ['ChkEmpty', t('Предзаказ не выбран')],
                'attr' => [[
                    'placeholder' => t('id, номер предзаказа, ФИО, email, телефон')
                ]]
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
        return $tab_form['reservation_id'];
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
        $this->linked_object = new OneClickItem($this->linked_object_id);
    }

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    public function getLinkText()
    {
        if ($this->linked_object['id']) {
            return t('Предзаказ N%num от %date', [
                'num' => $this->linked_object['id'],
                'date' => date('d.m.Y', strtotime($this->linked_object['dateof']))
            ]);
        } else {
            return t('Предзаказ не найден (ID: %id)', [
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
            $url = Manager::obj()->getAdminUrl('edit', ['id' => $this->linked_object->id], 'shop-reservationctrl');
            return $url;
        }
    }

    /**
     * Возвращает true, если объект находится на другом сайте
     *
     * @return bool
     */
    public function isObjectOtherSite()
    {
        $current_site_id = \RS\Site\Manager::getSiteId();
        return $this->linked_object['id'] && ($this->linked_object['site_id'] != $current_site_id);
    }
}