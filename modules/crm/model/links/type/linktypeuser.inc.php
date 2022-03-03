<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Links\Type;

use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type\User as TypeUser;
use RS\Router\Manager;
use Shop\Model\Orm\Order;
use Users\Model\Orm\User;

/**
 * Связь объекта с заказом
 */
class LinkTypeUser extends AbstractType
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
        return t('Пользователь');
    }

    /**
     * Возвращает объект формы, который следует отобразить для указания параметров связывания
     *
     * @return FormObject
     */
    public function getTabForm()
    {
        $form = new FormObject(new PropertyIterator([
            'user_id' => new TypeUser([
                'description' => t('Поиск по пользователю'),
                'checker' => ['ChkEmpty', t('Пользователь не выбран')],
                'attr' => [[
                    'placeholder' => t('e-mail, ФИО')
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
        return $tab_form['user_id'];
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

        $this->linked_object = new User($this->linked_object_id);
    }

    /**
     * Возвращает текст, который нужно отобразить при визуализации связи
     *
     * @return mixed
     */
    public function getLinkText()
    {
        if ($this->linked_object['id']) {
            return t('Пользователь %user_fio', [
                'user_fio' => $this->linked_object->getFio(),
            ]);
        } else {
            return t('Пользователь не найден (ID: %id)', [
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
            $url = Manager::obj()->getAdminUrl('edit', ['id' => $this->linked_object->id], 'users-ctrl');
            return $url;
        }
    }
}