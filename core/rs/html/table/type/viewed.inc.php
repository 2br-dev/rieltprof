<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Html\Table\Type;
use Main\Model\NoticeSystem\MeterApiInterface;
use Main\Model\NoticeSystem\ReadedItemApi;
use RS\Exception;
use RS\Router\Manager;

/**
 * Класс отвечает за колонку "был просмотрен". Если объект не был просмотрен, то отображается красная точка
 * @package RS\Html\Table\Type
 */
class Viewed extends AbstractType
{
    protected
        $head_template = 'system/admin/html_elements/table/coltype/viewed_head.tpl',
        $body_template = 'system/admin/html_elements/table/coltype/viewed.tpl',

        $meter_id,
        $viewed_ids = [],
        $viewed_last_id = 0,
        $view_all_url,
        $view_one_url,
        $object_id_field = 'id',
        $viewed_value = true;

    /**
     * Viewed constructor.
     * @param string|null $field поле, в котором находится флаг о прочтении объекта.
     * @param
     * @param string $title название колонки
     * @param array $property дополнительные опции
     */
    function __construct($field, $meter_api, $title = null, $property = null)
    {
        if ($title === null) {
            $title = t('Был просмотрен');
        }

        $this->setTdAttr([
            'class' => 'cell-is-viewed'
        ]);
        $this->setThAttr([
            'class' => 'cell-is-viewed'
        ]);

        if ($meter_api instanceof MeterApiInterface) {
            $this->meter_id = $meter_api->getMeterId();
        } else {
            throw new Exception(t('Ожидался объект, потомок MeterApiInterface'));
        }

        $router = Manager::obj();

        //Устанавливаем стандартные URL для CRUD контроллеров (потомков \RS\Controller\Admin\Helper\CrudCollection)
        $this->setViewAllUrl($router->getAdminUrl('markAllAsViewed'));
        $this->setViewOneUrlPattern($router->getAdminPattern('markOneAsViewed', [':id' => '@'.$this->object_id_field]));

        parent::__construct($field, $title, $property);
    }

    /**
     * Вызывается во время установки данных в таблицу
     *
     * @param $data
     */
    function onSetData($data)
    {
        if ($this->field === null) {
            $ids = [];
            foreach($data as $item) {
                $ids[] = $item[$this->object_id_field];
            }
            $readed_api = new ReadedItemApi();

            $this->viewed_last_id = $readed_api->getLastReadedId($this->getMeterId());

            if ($ids) {
                $this->viewed_ids = $readed_api->getReadedIds($this->getMeterId(), $ids);
            }
        }
    }

    /**
     * Возвращает ID счетчика
     *
     * @return string
     */
    function getMeterId()
    {
        return $this->meter_id;
    }

    /**
     * Поле, в котором находится ID
     * @param string $field
     */
    function setObjectIdField($field)
    {
        $this->object_id_field = $field;
    }

    /**
     * Устанавливает URL для ссылки "Отметить все прочитанным"
     *
     * @param string $url
     * @return void
     */
    function setViewAllUrl($url)
    {
        $this->view_all_url = $url;
    }

    function getViewAllUrl()
    {
        return $this->view_all_url;
    }

    /**
     * Устанавливает URL, переход по которому будет означать прочтение объекта
     *
     * @param string $url
     * @return void
     */
    function setViewOneUrlPattern($url)
    {
        $this->view_one_url = $url;
    }

    function getViewOneUrl()
    {
        return $this->getHref($this->view_one_url);
    }

    /**
     * Устанавливает, какое значение должно означать, что объект прочитан.
     *
     * @param mixed $value
     * @return void
     */
    function setViewedValue($value)
    {
        $this->viewed_value = $value;
    }

    /**
     * Возвращает, какое значение должно означать, что объект прочитан.
     *
     * @return mixed
     */
    function getViewedValue()
    {
        return $this->viewed_value;
    }

    /**
     * Возвращает true, если объект был просмотрен
     */
    function isViewed()
    {
        if ($this->field) {
            return $this->value == $this->getViewedValue();
        } else {
            //Ищем в подгруженных данных
            $value = $this->row[$this->object_id_field];

            return ($value <= $this->viewed_last_id)
                    ||  in_array($value, $this->viewed_ids);
        }
    }

    /**
     * Возвращает ID тегущего объекта
     *
     * @return integer
     */
    function getObjectId()
    {
        return $this->row[$this->object_id_field];
    }
}